@extends('layouts.app_vendor')

@section('title', 'อัปโหลดสลิป')
@section('content')
<div class="container">
    <h2>อัปโหลดสลิปสำหรับการจอง #{{ $booking->id }}</h2>
    <p>ล็อก: {{ $booking->stall->stall_code }} | โซน: {{ $booking->stall->zone->zone_name }}</p>

    <form action="{{ url('vendor/booking/'.$booking->id.'/upload-slip') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label for="slip">เลือกไฟล์สลิป:</label>
        <input type="file" name="slip" required>
        <button type="submit" class="btn">อัปโหลด</button>
    </form>
</div>
@endsection
