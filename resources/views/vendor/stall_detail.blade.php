@extends('layouts.app_vendor')

@section('title', 'รายละเอียดล็อก')
@section('content')
<div class="container">
    <h2>รายละเอียดล็อก {{ $stall->stall_code }}</h2>

    <p><strong>โซน:</strong> {{ $stall->zone->zone_name }}</p>
    <p><strong>ขนาด:</strong> {{ $stall->size }}</p>
    <p><strong>ราคา:</strong> {{ number_format($stall->price, 2) }} บาท</p>
    <p><strong>เงินมัดจำ:</strong> {{ number_format($stall->deposit, 2) }} บาท</p>
    <p><strong>ค่าน้ำ:</strong> {{ number_format($stall->water, 2) }} บาท</p>
    <p><strong>ค่าไฟ:</strong> {{ number_format($stall->electricity, 2) }} บาท</p>
    <p><strong>เงื่อนไข:</strong> {{ $stall->condition }}</p>
    <p><strong>สถานะ:</strong> {{ $stall->status }}</p>

    @if($stall->status === 'available')
        <form action="{{ route('vendor.stall.book', $stall->stall_id) }}" method="POST">
            @csrf
            <button type="submit" class="btn">จองล็อกนี้</button>
        </form>
    @else
        <p style="color:red;">ล็อกนี้ไม่ว่าง</p>
    @endif
</div>

@endsection
