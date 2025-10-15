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
            'month'        => 'required|integer|min:1|max:12',
            'acc_name'     => 'required|string|max:150',
            'bank'         => 'required|string|max:100',
            'payment_date' => 'required|date',
            'amount'       => 'required|numeric|min:0.01',
            'slip'         => 'required|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);
        $y = (int) $data['year'];
        $m = (int) $data['month'];
        $uid = Auth::id();

        $busy = Stall_Status::where('stall_id', $stall->stall_id)
            ->where('year', $y)->where('month', $m)
            ->whereIn('status_id', [Status::UNAVAILABLE, Status::PENDING, Status::CLOSED])
            ->exists();
        if ($busy || ! $stall->is_active) {
            return back()->withErrors(['month' => 'เดือนนี้ล็อกนี้ไม่ว่างแล้ว'])->withInput();
        }

        $path = $request->file('slip')->store('slips', 'public');

        DB::transaction(function () use ($uid, $stall, $y, $m, $data, $path) {
            $existing = Booking::where('user_id', $uid)
                ->where('year', $y)->where('month', $m)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                if ($existing->stall_id !== $stall->stall_id) {
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

                $existing->update(['status_id' => Status::CANCEL]);
                $existing->delete(); 
            }

            $booking = Booking::create([
                'user_id'   => $uid,
                'stall_id'  => $stall->stall_id,
                'year'      => $y,
                'month'     => $m,
                'status_id' => Status::PENDING,
            ]);

            $booking->payments()->create([
                'acc_name'     => $data['acc_name'],
                'bank'         => $data['bank'],
                'payment_date' => $data['payment_date'],
                'amount'       => $data['amount'],
                'slip_path'    => $path,
            ]);

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
