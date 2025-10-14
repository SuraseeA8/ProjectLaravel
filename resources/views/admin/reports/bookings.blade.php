@extends('layouts.app')

@section('title', 'รายงานการจอง')

@section('content')
    <div class="container">
        <div class="h-center">
            <h2 class="mb-4 text-center">รายงานการจองประจำเดือน</h2>

            <form method="GET" action="{{ route('admin.reports.bookings') }}" class="month-form">
                <label>เดือน :</label>

                <select name="month">
                    @foreach (range(1, 12) as $mm)
                        <option value="{{ $mm }}" {{ $mm == request('month') ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, $mm)->locale('th')->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>

                <input type="number" name="year" value="{{ request('year', now()->year) }}" min="2025" max="2100">
                <button type="submit">ค้นหา</button>
            </form>
        </div>

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

    /* ฟอร์มเลือกเดือน (เหมือนหน้าเลือกล็อก) */
    .month-form {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 12px;
        padding: 12px 20px;
    }

    .month-form label {
        font-weight: 600;
        color: #333;
    }

    .month-form select,
    .month-form input[type="number"] {
        padding: 6px 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
    }

    .month-form button {
        padding: 6px 14px;
        background: #E68F36;
        color: #fff;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        transition: background 0.3s;
    }

    .month-form button:hover {
        background: #d87c2e;
    }

    /* การ์ดตาราง */
    .report-card {
        padding: 20px;
        border-radius: 15px;
        background: #fff;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        margin-bottom: 25px;
    }

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