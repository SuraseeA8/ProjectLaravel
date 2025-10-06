<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function bookingReport(Request $request)
    {
        // รับค่าจาก input type="month" เช่น 2025-04
        $selectedMonth = $request->input('month');

        if ($selectedMonth) {
            [$year, $month] = explode('-', $selectedMonth);

            // Query การจอง
            $bookings = DB::table('bookings')
                ->join('users', 'bookings.user_id', '=', 'users.id')
                ->join('stalls', 'bookings.stall_id', '=', 'stalls.stall_id')
                ->leftJoin('shop_detail', 'bookings.user_id', '=', 'shop_detail.user_id')
                ->select(
                    'stalls.stall_code',
                    DB::raw("CONCAT(users.users_fname, ' ', users.users_lname) as fullname"),
                    'shop_detail.shop_name',
                    'shop_detail.description as shop_detail'
                )
                ->where('bookings.year', $year)
                ->where('bookings.month', $month)
                ->get();

            // นับสรุปผล
            $totalStalls = DB::table('stalls')->count();
            $totalBooked = $bookings->count();
            $totalAvailable = $totalStalls - $totalBooked;
        } else {
            $year = now()->year;
            $month = now()->month;
            $bookings = collect([]);
            $totalStalls = 0;
            $totalBooked = 0;
            $totalAvailable = 0;
        }

        return view('admin.reports.bookings', compact(
            'bookings',
            'year',
            'month',
            'selectedMonth',
            'totalStalls',
            'totalBooked',
            'totalAvailable'
        ));
    }
}
