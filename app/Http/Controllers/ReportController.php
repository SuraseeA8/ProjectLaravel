<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Stall;
use App\Models\Status; // เพื่ออ้างอิงคอนสแตนท์สถานะ

class ReportController extends Controller
{
    public function bookingReport(Request $request)
    {
        $month = (int) $request->input('month'); // 1..12
        $year  = (int) $request->input('year');  // ค.ศ. เช่น 2025

        if ($month && $year) {

            // ดึงเฉพาะใบจองที่ยัง "ใช้งานอยู่"
            // - ไม่ถูก soft delete (Eloquent กันให้โดยค่าเริ่มต้น ถ้าใช้ SoftDeletes)
            // - ไม่ใช่สถานะยกเลิก
            $baseQuery = Booking::query()
                ->with([
                    'user',        // ถ้า Model user ใช้ SoftDeletes จะไม่ดึงที่ถูกลบมาให้อยู่แล้ว
                    'stall',
                    'shopDetail',  // เช่น ความสัมพันธ์ข้อมูลร้าน
                ])
                ->where('year',  $year)
                ->where('month', $month)
                ->when(defined(Status::class . '::CANCEL'), function ($q) {
                    $q->where('status_id', '!=', Status::CANCEL);
                }, function ($q) {
                    // ถ้ายังไม่มีคอนสแตนท์ CANCEL ให้กันด้วย id 5 (แก้ให้ตรงระบบคุณ)
                    $q->where('status_id', '!=', 5);
                });

            $rows = $baseQuery->get();

            // แปลงเป็น array สำหรับส่งไป View
            $bookings = $rows->map(function ($booking) {
                return [
                    'stall_code'  => $booking->stall->stall_code ?? '-',
                    'fullname'    => $booking->user
                                    ? trim(($booking->user->users_fname ?? '').' '.($booking->user->users_lname ?? ''))
                                    : '-',
                    'shop_name'   => $booking->shopDetail->shop_name ?? '-',
                    'shop_detail' => $booking->shopDetail->description ?? '-',
                ];
            });

            // สรุปผล (นับแบบเดียวกับรายการด้านบน)
            $totalBooked = (clone $baseQuery)->count();

            $totalStalls    = Stall::count();
            $totalAvailable = max(0, $totalStalls - $totalBooked);

            return view('admin.reports.bookings', [
                'bookings'       => $bookings,
                'year'           => $year,
                'month'          => $month,
                'totalBooked'    => $totalBooked,
                'totalAvailable' => $totalAvailable,
            ]);
        }

        // ยังไม่เลือกเดือน/ปี
        return view('admin.reports.bookings', [
            'bookings'       => [],
            'year'           => null,
            'month'          => null,
            'totalBooked'    => 0,
            'totalAvailable' => 0,
        ]);
    }
}
