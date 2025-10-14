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
        $this->closestall($stall);

        $data = $request->validate([
            'year'   => 'required|integer|min:2000|max:2100',
            'month'  => 'required|integer|min:1|max:12',
            'amount' => 'nullable|numeric|min:0',
            'slip'   => 'required|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);
        $y = (int) $data['year'];
        $m = (int) $data['month'];
        $uid = Auth::id();

        $busy = Stall_Status::where('stall_id', $stall->stall_id) 
            ->where('year', $y)->where('month', $m)
            ->whereIn('status_id', [Status::UNAVAILABLE, Status::PENDING, Status::CLOSED])
            ->exists();
        if ($busy) {
            return back()->withErrors(['month' => 'เดือนนี้ล็อกนี้ไม่ว่างแล้ว'])->withInput();
        }

        $path = $request->file('slip')->store('slips', 'public');

        DB::transaction(function () use ($uid, $stall, $y, $m) {
            $existing = Booking::where('user_id', $uid)
                ->where('year', $y)->where('month', $m)
                ->lockForUpdate()
                ->first();

            if ($existing && $existing->stall_id !== $stall->stall_id) {
                Stall_Status::where('stall_id', $existing->stall_id)
                    ->where('year', $y)->where('month', $m)
                    ->update([
                        'status_id'  => Status::AVAILABLE,
                        'booking_id' => null,
                        'user_id'    => null,
                        'reason'     => 'สลับไปจองล็อกอื่น',
                        'updated_at' => now(),
                    ]);
            }

            $booking = Booking::updateOrCreate(
                ['user_id' => $uid, 'year' => $y, 'month' => $m],
                ['stall_id' => $stall->stall_id, 'status_id' => Status::PENDING]
            );

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

        return redirect()->route('vendor.booking.status')
            ->with('ok', 'ยื่นจองสำเร็จ (อัปเดตใบจองเดือนนี้ให้ล็อกนี้แล้ว) กรุณารอการตรวจสอบ');
    }




    public function bookingStatus(Request $request)
    {
        $items = Booking::with(['stall.zone', 'status', 'payments'])
            ->withTrashed()
            ->where('user_id', Auth::id())
            ->latest()->paginate(12)->withQueryString();


        return view('vendor.booking_status', compact('items'));
    }

    public function cancelBooking(Request $request, Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }
        if ($booking->status_id !== Status::PENDING) {
            return back()->withErrors(['cancel' => 'อนุมัติแล้วหรือยกเลิกไปแล้ว ยกเลิกไม่ได้']);
        }

        DB::transaction(function () use ($booking) {
            $booking->update(['status_id' => Status::CANCEL]);

            Stall_Status::where('stall_id', $booking->stall_id)
                ->where('year', $booking->year)
                ->where('month', $booking->month)
                ->update([
                    'status_id'  => Status::AVAILABLE,
                    'reason'     => 'ผู้ใช้ยกเลิก',
                    'updated_at' => now(),
                ]);

            $booking->delete(); 
        });

        return back()->with('ok', 'ยกเลิกใบจองเรียบร้อย');
    }


    private function closestall(Stall $stall): void
    {
        if (! $stall->is_active) {
            abort(403, 'ล็อกนี้ปิดใช้งาน');
        }
    }
}
