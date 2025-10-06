@extends('layouts.app_vendor')

@section('title', 'รายการจอง')

@section('content')
<main class="container">

    {{-- Flash / Errors --}}
    @if (session('ok'))
        <div class="alert ok">{{ session('ok') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert err">
        @foreach($errors->all() as $e) <div>{{ $e }}</div> @endforeach
        </div>
    @endif

    <h2 class="mb-3">รายการจองของฉัน</h2>

    <table border="1" cellpadding="10" style="margin:auto; width:100%; max-width:1000px;">
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
                        <a href="{{ asset('storage/'.$payment->slip_path) }}" target="_blank">ดูสลิป</a>            
                        @else{{-- ถ้าใช้ flow ใหม่ (Checkout เท่านั้น) และใบจองนี้ไม่มีสลิป ให้แสดงขีด --}}
                            —
                        @endif
                        {{-- หรือถ้าอยากให้ไปหน้า checkout ของล็อกนั้นอีกครั้ง (flow ใหม่) ให้ใช้ลิงก์นี้แทน:
                        <a href="{{ route('vendor.stall.checkout', $booking->stall_id) }}?year={{ $booking->year }}&month={{ $booking->month }}">อัปโหลด</a>
                        --}}
                        
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
        {{-- ตัวแบ่งหน้า --}}
        {{ $items->links() }}
    </div>

</main>
@endsection
