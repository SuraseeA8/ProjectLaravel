<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Stall;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Status;

class VendorController extends Controller
{   
    // ✅ แสดงหน้าหลักของ Vendor
    public function showHome()
    {
        return view('vendor.home'); 
    }

    /**
     * แสดงหน้ารายการล็อก (ตรวจสอบสถานะ)
     */
    public function stallList()
    {
        // ดึงล็อกจาก DB + โซน
        $stalls = Stall::with('zone')->orderBy('stall_id', 'asc')->get();
        return view('vendor.stalls', compact('stalls'));
    }

    /**
     * แสดงรายละเอียดล็อก
     */
    public function stallDetail($id)
    {
        $stall = Stall::with('zone')->findOrFail($id);
        return view('vendor.stall_detail', compact('stall'));
    }

    /**
     * จองล็อก
     */
    public function bookStall(Request $request, $id)
    {
        $stall = Stall::findOrFail($id);

        if ($stall->status != 'available') {
            return redirect()->back()->with('error', 'ล็อกนี้ไม่ว่างแล้ว');
        }

        // หา status_id ของ "pending"
        $statusPending = Status::where('name', 'pending')->first();

        $booking = Booking::create([
            'user_id'   => Auth::id(),
            'stall_id'  => $stall->stall_id,
            'month'     => date('m'),
            'year'      => date('Y'),
            'status_id' => $statusPending->id ?? 1, // fallback ถ้าไม่เจอ
        ]);

        // อัปเดตสถานะล็อก
        $stall->update(['status' => 'pending']);

        return redirect()->route('vendor.booking.slip', $booking->id)
            ->with('success', 'ทำการจองเรียบร้อย กรุณาอัปโหลดสลิป');
    }

    /**
     * ฟอร์มอัปโหลดสลิป
     */
    public function uploadSlipForm($id)
    {
        $booking = Booking::with('stall')->findOrFail($id);
        return view('vendor.upload_slip', compact('booking'));
    }

    /**
     * บันทึกสลิป
     */
    public function storeSlip(Request $request, $id)
    {
        $request->validate([
            'slip' => 'required|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $booking = Booking::with('stall')->findOrFail($id);

        $path = $request->file('slip')->store('slips', 'public');

        // หา status_id ของ "pending"
        $statusPending = Status::where('name', 'pending')->first();

        Payment::create([
            'booking_id' => $booking->id,
            'amount'     => $booking->stall->price + $booking->stall->deposit,
            'slip_image' => $path,
            'status_id'  => $statusPending->id ?? 1,
        ]);

        return redirect()->route('vendor.booking.status')
            ->with('success', 'อัปโหลดสลิปเรียบร้อย รอการตรวจสอบ');
    }

    /**
     * แสดงสถานะการจอง
     */
    public function bookingStatus()
    {
        $bookings = Booking::with(['stall', 'payment', 'status'])
            ->where('user_id', Auth::id())
            ->get();

        return view('vendor.booking_status', compact('bookings'));
    }

    /**
     * ยกเลิกการจอง
     */
    public function cancelBooking($id)
    {
        $booking = Booking::with('stall')->findOrFail($id);

        if ($booking->user_id != Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // หา status_id ของ "cancelled"
        $statusCancelled = Status::where('name', 'cancelled')->first();

        $booking->update(['status_id' => $statusCancelled->id ?? 3]);
        $booking->stall->update(['status' => 'available']);

        return redirect()->route('vendor.booking.status')
            ->with('success', 'ยกเลิกการจองเรียบร้อยแล้ว');
    }

    public function eventBoard()
    {
        return view('vendor.events');
    }
}
