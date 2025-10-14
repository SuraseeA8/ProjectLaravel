@extends('layouts.app')

@section('title', 'รายการจอง')

@section('content')
    <main class="container">

        <h2 class="mb-3">รายการจองของฉัน</h2>

        <table border="1" cellpadding="10" style="margin:auto; width:100%; max-width:1000px;">
            <thead>
                <tr>
                    <th>ล็อก</th>
                    <th>โซน</th>
                    <th>เดือน</th>
                    <th>ปี</th>.
                    <th>สถานะ</th>
                    <th>สลิป</th>
                    <th>ยกเลิก</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $booking)
                    @php
                        // หา payment ล่าสุด (ถ้ามี)
                        $payment = optional($booking->payments)->last();
                        // แปลงชื่อเดือนเป็นไทย
                        $monthName = \Carbon\Carbon::create($booking->year, $booking->month)->locale('th')->translatedFormat('F');
                        // สถานะ (ใช้ชื่อจากตาราง status ถ้ามีคอลัมน์ 'status_name', เปลี่ยนให้ตรง schema คุณ)
                        $statusText = $booking->status->status_name ?? $booking->status->name ?? '-';
                    @endphp

                    <tr>
                        <td>{{ $booking->stall->stall_code ?? '-' }}</td>
                        <td>{{ $booking->stall->zone->zone_name ?? '-' }}</td>
                        <td>{{ $monthName }}</td>
                        <td>{{ $booking->year }}</td>
                        <td>{{ $statusText }}</td>
                        <td>
                            @if ($payment && $payment->slip_path)
                                <a href="{{ asset('storage/' . $payment->slip_path) }}" target="_blank">ดูสลิป</a>
                            @else
                                —
                            @endif
                            
                        </td>
                        <td>
                            @if ($booking->status_id === \App\Models\Status::PENDING)
                                <form action="{{ route('vendor.booking.cancel', $booking->booking_id) }}" method="POST"
                                    onsubmit="return confirm('ยืนยันยกเลิกใบจองนี้?');">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">ยกเลิก</button>
                                </form>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">ยังไม่มีการจอง</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-3">
            {{ $items->links() }}
        </div>

    </main>
@endsection

<style>
    body {
        background: #fffde7;
        font-family: 'Kanit', sans-serif;
    }

    main.container {
        max-width: 1100px;
        margin: 30px auto;
        padding: 20px;
        background: #fff;
        border-radius: 12px;
        border: 2px solid #E68F36;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
    }

    h2.mb-3 {
        text-align: center;
        font-size: 24px;
        font-weight: bold;
        color: #E68F36;
        margin-bottom: 20px;
    }

    /* การ์ดตาราง */
    table {
        width: 100%;
        border-collapse: collapse;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        background: #fff;
        margin-bottom: 25px;
    }

    table thead {
        background: linear-gradient(90deg, #E68F36, #f2a65a);
        color: #fff;
    }

    table th {
        padding: 14px;
        text-align: center;
        font-size: 15px;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    table td {
        border: 1px solid #f0d9c3;
        padding: 12px;
        text-align: center;
        font-size: 14px;
        color: #333;
    }

    /* ปุ่ม */
    .btn {
        display: inline-block;
        padding: 8px 14px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 500;
        text-decoration: none;
        cursor: pointer;
        transition: 0.3s;
    }

    .btn-danger {
        background: #f44336;
        color: #fff;
        border: none;
    }

    .btn-danger:hover {
        background: #d32f2f;
    }

    /* ลิงก์สลิป */
    td a {
        color: #E68F36;
        text-decoration: none;
        font-weight: 500;
    }

    td a:hover {
        text-decoration: underline;
    }

    /* Flash Message */
    .alert.ok {
        background: #e8f5e9;
        border: 1px solid #4CAF50;
        color: #2e7d32;
        padding: 10px;
        border-radius: 8px;
        text-align: center;
        margin-bottom: 15px;
    }

    .alert.err {
        background: #ffebee;
        border: 1px solid #f44336;
        color: #b71c1c;
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 15px;
    }

    .text-center {
        text-align: center;
        font-size: 14px;
        padding: 15px;
    }

    /* Pagination */
    .mt-3 nav {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }

    .mt-3 nav ul {
        list-style: none;
        display: flex;
        gap: 6px;
        padding: 0;
    }

    .mt-3 nav ul li a,
    .mt-3 nav ul li span {
        display: block;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 13px;
        color: #333;
        border: 1px solid #ddd;
        background: #fff;
    }

    .mt-3 nav ul li.active span {
        background: #E68F36;
        color: #fff;
        border-color: #E68F36;
    }

    .mt-3 nav ul li a:hover {
        background: #f9f9f9;
    }
</style>