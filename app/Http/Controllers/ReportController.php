<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Stall;

class ReportController extends Controller
{
    public function bookingReport(Request $request)
    {
        $selectedMonth = $request->input('month');

        if ($selectedMonth) {
            [$year, $month] = explode('-', $selectedMonth);

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

        return view('admin.reports.bookings', [
            'bookings' => [],
            'year' => null,
            'month' => null,
            'totalBooked' => 0,
            'totalAvailable' => 0
        ]);
    }
}