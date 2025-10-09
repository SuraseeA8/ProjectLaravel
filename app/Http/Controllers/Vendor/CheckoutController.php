<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Booking;
use App\Models\Stall;
use App\Models\Status;
use App\Models\Stall_Status;

class CheckoutController extends Controller
{
    public function checkoutForm(Request $request, Stall $stall)
    {
        $y = (int) $request->query('year');
        $m = (int) $request->query('month');

        $monthStatus = Stall_Status::where('stall_id', $stall->stall_id)
            ->where('year', $y)->where('month', $m)->first();

        $hasMyBookingThisMonth = Booking::where('user_id', Auth::id())
            ->where('year', $y)->where('month', $m)
            ->whereIn('status_id', [Status::PENDING, Status::UNAVAILABLE])
            ->exists();

        $canBook = $stall->is_active
            && ! $hasMyBookingThisMonth
            && ! ($monthStatus && in_array($monthStatus->status_id, [
                Status::UNAVAILABLE,
                Status::PENDING,
                Status::CLOSED
            ]));

        if (! $canBook) {
            return redirect()->route('vendor.stall.detail', $stall->stall_id)
                ->withErrors(['slip' => 'เงื่อนไขการจองไม่ผ่าน'])->withInput();
        }

        return view('vendor.upload_slip', compact('stall', 'y', 'm'));
    }

    public function checkoutSubmit(Request $request, Stall $stall)
    {
        $data = $request->validate([
            'year'         => 'required|integer|min:2000|max:2100',
            'month'        => 'required|integer|between:1,12',
            'acc_name'     => 'required|string|max:150',
            'bank'         => 'required|string|max:100',
            'payment_date' => 'required|date',
            'amount'       => 'required|numeric|min:0.01',
            'slip'         => 'required|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        $y   = (int) $data['year'];
        $m   = (int) $data['month'];
        $uid = Auth::id();

        // ต้องว่างจริง (ไม่ใช่ 2/3/4) และล็อกต้อง active
        $busy = Stall_Status::where('stall_id', $stall->stall_id)
            ->where('year', $y)->where('month', $m)
            ->whereIn('status_id', [Status::UNAVAILABLE, Status::PENDING, Status::CLOSED])
            ->exists();

        if ($busy || ! $stall->is_active) {
            return back()->withErrors(['month' => 'เดือนนี้ล็อกนี้ไม่ว่างแล้ว'])->withInput();
        }

        // เก็บสลิป
        $path = $request->file('slip')->store('slips', 'public');

        DB::transaction(function () use ($uid, $stall, $y, $m, $data, $path) {
            // 1) หา "ใบจองที่ยังไม่ถูกลบ" ของเดือนนี้
            $active = Booking::where('user_id', $uid)
                ->where('year', $y)->where('month', $m)
                ->lockForUpdate()
                ->first();

            if ($active) {
                // คืนสถานะล็อกเดิมให้ว่าง (อย่าเซ็ต FK เป็น null ถ้า schema ไม่อนุญาต)
                Stall_Status::where('stall_id', $active->stall_id)
                    ->where('year', $y)->where('month', $m)
                    ->update([
                        'status_id'  => Status::AVAILABLE,
                        'reason'     => 'สลับ/ออกจากล็อกเดิม',
                        'updated_at' => now(),
                    ]);

                // เก็บประวัติ: เปลี่ยนเป็น CANCEL แล้ว soft delete
                $active->update(['status_id' => Status::CANCEL]);
                $active->delete(); // soft delete (ตั้ง deleted_at)
            }

            // 2) สร้าง "แถวใหม่" ทุกครั้ง (ไม่ไปรีสโตร์อันเก่า)
            $booking = Booking::create([
                'user_id'   => $uid,
                'stall_id'  => $stall->stall_id,
                'year'      => $y,
                'month'     => $m,
                'status_id' => Status::PENDING, // รออนุมัติ
            ]);
            $booking->refresh(); // ให้แน่ใจว่ามี booking_id

            // 3) แนบสลิปเป็น payment
            $booking->payments()->create([
                'acc_name'     => $data['acc_name'],
                'bank'         => $data['bank'],
                'payment_date' => $data['payment_date'],
                'amount'       => $data['amount'],
                'slip_path'    => $path,
            ]);

            // 4) ตั้งสถานะล็อกเดือนนี้เป็น PENDING และผูก booking ใหม่
            Stall_Status::updateOrCreate(
                ['stall_id' => $stall->stall_id, 'year' => $y, 'month' => $m],
                [
                    'status_id'  => Status::PENDING,
                    'booking_id' => $booking->booking_id,
                    'user_id'    => $uid,
                    'reason'     => 'รอยืนยันสลิป',
                    'updated_at' => now(),
                ]
            );
        });

        return redirect()->route('vendor.booking.status', [
            'range' => 'month',
            'year'  => $y,
            'month' => $m,
        ])->with('ok', 'ยืนยันการจองเรียบร้อย (ปรับใบจองเดือนนี้ให้ล็อกนี้แล้ว) รอแอดมินตรวจสอบ');
    }
}
