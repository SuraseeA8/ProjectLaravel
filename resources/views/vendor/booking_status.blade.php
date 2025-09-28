@extends('layouts.app_vendor')

@section('title', 'สถานะการจอง')
@section('content')
<div class="container">
    <h2>สถานะการจองของฉัน</h2>

    <table border="1" cellpadding="10" style="margin:auto; width:80%;">
        <thead>
            <tr>
                <th>ล็อก</th>
                <th>โซน</th>
                <th>เดือน</th>
                <th>ปี</th>
                <th>สถานะ</th>
                <th>สลิป</th>
                <th>ยกเลิก</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $booking)
                <tr>
                    <td>{{ $booking->stall->stall_code }}</td>
                    <td>{{ $booking->stall->zone->zone_name }}</td>
                    <td>{{ $booking->month }}</td>
                    <td>{{ $booking->year }}</td>
                    <td>{{ $booking->status->name }}</td>
                    <td>
                        @if($booking->payment)
                            <a href="{{ asset('storage/'.$booking->payment->slip_image) }}" target="_blank">ดูสลิป</a>
                        @else
                            <a href="{{ route('vendor.booking.slip', $booking->id) }}">อัปโหลด</a>
                        @endif
                    </td>
                    <td>
                        @if($booking->status->name === 'pending')
                            <form action="{{ route('vendor.booking.cancel', $booking->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger">ยกเลิก</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7">ยังไม่มีการจอง</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
