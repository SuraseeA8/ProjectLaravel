<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stall;
use App\Models\Stall_status;
use App\Models\Booking;
use App\Models\User;
use App\Models\ShopDetail;
use Illuminate\Support\Facades\Auth;

class StallManageController extends Controller
{
    public function index(Request $request)
    {
        
        $month = (int) $request->input('month', now()->month);
        $year  = (int) $request->input('year', now()->year);

        
        $stalls = Stall::all();
        $stallStatuses = Stall_status::all();

        
        return view('stallmanage', compact('stalls', 'stallStatuses', 'month', 'year'));
    }

    public function toggleStatus($stall_id, $month, $year)
    {
        
        $stallStatus = Stall_status::where('stall_id', $stall_id)
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        if ($stallStatus) {

            $stallStatus->status_id = ($stallStatus->status_id == 5) ? 1 : 5;
            $stallStatus->save();

        } else {

            Stall_status::create([
                'stall_id' => $stall_id,
                'status_id' => 5,
                'month' => $month,
                'year' => $year,
                'user_id'  => Auth::id(),
            ]);
        }

        return redirect()->back()->with('success', 'อัปเดตสถานะล็อกเรียบร้อยแล้ว');
    }

    public function manage()
    {

        $bookings = Booking::with(['user.shopDetail', 'stall', 'payment'])
            ->where('status_id', 3)
            ->get();

        $shops = ShopDetail::all();

        return view('bookingsmanage', compact('bookings', 'shops'));
    }


    public function approve($id)
    {
        $booking = Booking::find($id);
        if ($booking) {
            $booking->status_id = 2; 
            $booking->save();
        }

        return redirect()->route('admin.booking.manage');
    }


    public function cancel($id)
    {
        $booking = Booking::find($id);
        if ($booking) {
            $booking->status_id = 1; 
            $booking->save();
        }

        return redirect()->route('admin.booking.manage');
    }

}