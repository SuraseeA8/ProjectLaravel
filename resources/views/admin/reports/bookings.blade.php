@extends('layouts.app')

@section('title', 'รายงานการจอง')

@section('content')
    <div class="container">
        <div class="h-center">
            <h2 class="mb-4 text-center">รายงานการจองประจำเดือน</h2>

            {{-- ฟอร์มเลือกเดือน --}}
            <form method="GET" action="{{ route('admin.reports.bookings') }}" class="text-center mb-3">
                <label for="month">เลือกเดือน:</label>
                <input type="month" id="month" name="month" value="{{ request('month') }}">
                <button type="submit" class="btn btn-warning btn-sm">ค้นหา</button>
            </form>
        </div>

        {{-- ตารางรายงาน --}}
        @if(!empty($bookings))
            <div class="report-card">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>รหัสล็อก</th>
                            <th>ชื่อผู้จอง</th>
                            <th>ชื่อร้าน</th>
                            <th>รายละเอียดร้าน</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                            <tr>
                                <td>{{ $booking['stall_code'] }}</td>
                                <td>{{ $booking['fullname'] }}</td>
                                <td>{{ $booking['shop_name'] }}</td>
                                <td>{{ $booking['shop_detail'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- กล่องสรุปผล --}}
            <div class="report-summary">
                <h3>สรุปผลการจอง</h3>
                <p>เดือน / ปี :
                    <span>{{ \Carbon\Carbon::createFromDate($year, $month, 1)->locale('th')->isoFormat('MMMM YYYY') }}</span>
                </p>
                <p>จองแล้ว : <span>{{ $totalBooked }}</span></p>
                <p>ว่าง : <span>{{ $totalAvailable }}</span></p>
            </div>
        @else
            <p class="t-center">ยังไม่มีข้อมูลการจองสำหรับเดือนนี้</p>
        @endif
    </div>
@endsection


<style>
    .h-center {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        padding: 20px 0;
    }

    .t-center {
        text-align: center;
    }

    /* กล่องรวมทั้งหมด */
    .report-container {
        margin-top: 30px;
    }

    /* หัวข้อ */
    .report-title {
        font-size: 1.8rem;
        font-weight: bold;
        text-align: center;
        color: #E68F36;
        margin-bottom: 25px;
    }

    /* ฟอร์มเลือกเดือน */
    .report-form {
        text-align: center;
    }

    .report-form label {
        font-weight: 500;
        margin-right: 10px;
    }

    /* การ์ดตาราง */
    .report-card {
        padding: 20px;
        border-radius: 15px;
        background: #fff;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        margin-bottom: 25px;
    }

    /* ตาราง */
    .report-table {
        width: 100%;
        border-collapse: collapse;
        border-radius: 10px;
        overflow: hidden;
    }

    .report-table th {
        background: linear-gradient(90deg, #E68F36, #f2a65a);
        color: #fff;
        padding: 14px;
        text-align: center;
        font-size: 15px;
        letter-spacing: 0.5px;
    }

    .report-table td {
        border: 1px solid #f0d9c3;
        padding: 12px;
        text-align: center;
        font-size: 14px;
        color: #333;
    }

    .report-table tr:nth-child(even) {
        background: #FFF9F3;
    }

    .report-table tr:hover {
        background: #FFF3E0;
        transition: background 0.3s ease-in-out;
    }

    /* กล่องสรุป */
    .report-summary {
        padding: 20px;
        border: 2px solid #E68F36;
        border-radius: 15px;
        background: #fff;
        max-width: 400px;
        margin: 0 auto;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .report-summary h3 {
        margin-bottom: 20px;
        color: #E68F36;
        font-weight: bold;
        font-size: 18px;
    }

    .report-summary p {
        margin: 10px 0;
        font-size: 15px;
    }

    /* ป้ายตัวเลข */
    .report-summary span {
        display: inline-block;
        background: #FFF3E0;
        border: 2px solid #E68F36;
        padding: 6px 18px;
        border-radius: 25px;
        font-weight: bold;
        color: #E68F36;
        font-size: 14px;
    }
</style>