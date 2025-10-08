<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Stall;

class ReportController extends Controller
{
    public function bookingReport(Request $request)
    {
        // รับค่าจากฟอร์มใหม่ (select เดือน + input ปี)
        $month = $request->input('month'); // เลขเดือน เช่น 10
        $year = $request->input('year');  // ปี ค.ศ. เช่น 2025

        if ($month && $year) {
            $bookings = Booking::with(['user', 'stall', 'shopDetail'])
                ->where('year', $year)
                ->where('month', $month)
                ->get()
                ->map(function ($booking) {
                    return [
                        'stall_code' => $booking->stall->stall_code ?? '-',
                        'fullname' => $booking->user ? $booking->user->users_fname . ' ' . $booking->user->users_lname : '-',
                        'shop_name' => $booking->shopDetail->shop_name ?? '-',
                        'shop_detail' => $booking->shopDetail->description ?? '-',
                    ];
                });

            // ✅ คำนวณสรุปผล
            $totalBooked = Booking::where('year', $year)
                ->where('month', $month)
                ->count();

            $totalStalls = Stall::count();
            $totalAvailable = $totalStalls - $totalBooked;

            return view('admin.reports.bookings', [
                'bookings' => $bookings,
                'year' => $year,
                'month' => $month,
                'totalBooked' => $totalBooked,
                'totalAvailable' => $totalAvailable
            ]);
        }

        // ถ้ายังไม่ได้เลือกเดือน/ปี
        return view('admin.reports.bookings', [
            'bookings' => [],
            'year' => null,
            'month' => null,
            'totalBooked' => 0,
            'totalAvailable' => 0
        ]);
    }

}