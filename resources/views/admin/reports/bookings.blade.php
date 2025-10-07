@extends('layouts.app')

@section('title', 'Home')
@section('content')
    <div class="report-header">
        <h1>รายงานการจอง</h1>

        {{-- ฟอร์มเลือกเดือน --}}
        <form method="GET" action="{{ route('admin.reports.bookings') }}">
            <label>เดือน :</label>
            <input type="month" name="month" value="{{ $selectedMonth ?? '' }}">
            <button type="submit">ค้นหา</button>
        </form>
    </div>


    @if(!empty($selectedMonth))
        <div class="report-card">
            <h2>รายงานการจองของเดือน
                {{ \Carbon\Carbon::createFromDate($year, $month, 1)->locale('th')->isoFormat('MMMM YYYY') }}
            </h2>

            <table class="report-table">
                <tr>
                    <th>ล็อก</th>
                    <th>ชื่อ - นามสกุล</th>
                    <th>ชื่อร้าน</th>
                    <th>รายละเอียดร้าน</th>
                </tr>
                @forelse($bookings as $b)
                    <tr>
                        <td>{{ $b->stall_code }}</td>
                        <td>{{ $b->fullname }}</td>
                        <td>{{ $b->shop_name }}</td>
                        <td>{{ $b->shop_detail }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">ไม่มีการจองในเดือนนี้</td>
                    </tr>
                @endforelse
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


    @endif
@endsection