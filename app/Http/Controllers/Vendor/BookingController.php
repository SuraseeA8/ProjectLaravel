<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


use App\Models\Stall_Status;
use App\Models\Status;
use App\Models\Booking;
use App\Models\Stall;

class BookingController extends Controller
{
    public function bookStall(Request $request, Stall $stall)
    {
        $this->authorizeActiveStall($stall);

        $data = $request->validate([
            'year'   => 'required|integer|min:2000|max:2100',
            'month'  => 'required|integer|min:1|max:12',
            'amount' => 'nullable|numeric|min:0',
            'slip'   => 'required|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        $y   = (int) $data['year'];
        $m   = (int) $data['month'];
        $uid = Auth::id();

        // ล็อกนั้นในเดือน/ปีนี้ต้องไม่อยู่ในสถานะ ไม่ว่าง/รอ/ปิด
        $busy = Stall_Status::where('stall_id', $stall->stall_id)
            ->where('year', $y)->where('month', $m)
            ->whereIn('status_id', [Status::UNAVAILABLE, Status::PENDING, Status::CLOSED])
            ->exists();

        if ($busy) {
            return back()->withErrors(['month' => 'เดือนนี้ล็อกนี้ไม่ว่างแล้ว'])->withInput();
        }

        // เก็บไฟล์สลิป (ถ้าจะใช้บันทึกเป็น payment ภายหลัง)
        $path = $request->file('slip')->store('slips', 'public');

        DB::transaction(function () use ($uid, $stall, $y, $m) {
            // จับ booking เดิมของ user เดือนนี้ (ล็อกแถวเพื่อกันแข่ง)
            $existing = Booking::where('user_id', $uid)
                ->where('year', $y)->where('month', $m)
                ->lockForUpdate()
                ->first();

            // ถ้ามีและเป็นคนละล็อก → ปลดสถานะล็อกเดิมให้ "ว่าง"
            if ($existing && $existing->stall_id !== $stall->stall_id) {
                // ❗ ไม่อัพเดต booking_id / user_id เป็น null (กันชน NOT NULL)
                Stall_Status::where('stall_id', $existing->stall_id)
                    ->where('year', $y)->where('month', $m)
                    ->update([
                        'status_id'  => Status::AVAILABLE,
                        'reason'     => 'สลับไปจองล็อกอื่น',
                        'updated_at' => now(),
                    ]);
            }

            // คืนชีพ/สร้าง booking เดือนนี้ (รองรับ soft deleted)
            $booking = Booking::withTrashed()
                ->firstOrNew(['user_id' => $uid, 'year' => $y, 'month' => $m]);

            if ($booking->trashed()) {
                $booking->restore();
            }

            // กำหนดค่าแล้วบันทึกให้มี booking_id แน่นอน
            $booking->stall_id  = $stall->stall_id;
            $booking->status_id = Status::PENDING; // 3
            $booking->save();
            $booking->refresh();

            // อัพเดตสถานะล็อกของเดือนนี้ให้ "รออนุมัติ" และผูก booking นี้
            Stall_Status::updateOrCreate(
                ['stall_id' => $stall->stall_id, 'year' => $y, 'month' => $m],
                [
                    'status_id'  => Status::PENDING,
                    'booking_id' => $booking->booking_id,  // FK ต้องไม่ null
                    'user_id'    => $uid,                  // ถ้าคอลัมน์นี้ NOT NULL
                    'reason'     => 'รอยืนยันสลิป',
                    'updated_at' => now(),
                ]
            );
        });

        return redirect()->route('vendor.booking.status')
            ->with('ok', 'ยื่นจองสำเร็จ (อัปเดตใบจองเดือนนี้ให้ล็อกนี้แล้ว) กรุณารอการตรวจสอบ');
    }

    /* ---------------------------
     * สถานะการจองของฉัน
     * --------------------------- */
    public function bookingStatus(Request $request)
{
    $items = \App\Models\Booking::withTrashed()   // ← เห็นทั้งที่ลบแบบ soft และที่ยังอยู่
        ->with(['stall.zone','status','payments'])
        ->where('user_id', Auth::id())
        ->orderByDesc('created_at')
        ->paginate(12);

    return view('vendor.booking_status', compact('items'));
}

    /* ---------------------------
     * ยกเลิกใบจอง (ของฉันเท่านั้น)
     * --------------------------- */
    public function cancelBooking(Request $request, Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }
        if ($booking->status_id !== Status::PENDING) {
            return back()->withErrors(['cancel' => 'อนุมัติแล้วหรือยกเลิกไปแล้ว ยกเลิกไม่ได้']);
        }

        DB::transaction(function () use ($booking) {
            // 1) เก็บประวัติด้วยการเปลี่ยนสถานะ
            $booking->update(['status_id' => Status::CANCEL]); // 5

            // 2) คืนล็อกให้ว่าง (อย่าเซ็ต FK เป็น null ถ้าคอลัมน์ NOT NULL)
            Stall_Status::where('stall_id', $booking->stall_id)
                ->where('year', $booking->year)->where('month', $booking->month)
                ->update([
                    'status_id'  => Status::AVAILABLE,
                    'reason'     => 'ผู้ใช้ยกเลิก',
                    'updated_at' => now(),
                ]);

            // 3) ซ่อนแถวด้วย Soft Delete (ตั้งค่า deleted_at)
            $booking->delete();   // <= สำคัญ!! ทำให้เป็น soft delete
        });

        return back()->with('ok', 'ยกเลิกใบจองเรียบร้อย');
    }


    private function authorizeActiveStall(Stall $stall): void
    {
        if (! $stall->is_active) {
            abort(403, 'ล็อกนี้ปิดใช้งาน');
        }
    }
}
