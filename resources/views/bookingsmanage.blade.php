<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>คำขออนุมัติการจอง</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat">
    <style>
        body { font-family: "Montserrat", sans-serif; }
    </style>
    
</head>
<body class="p-4">
{{-- resources/views/bookingsmanage.blade.php --}}

{{-- resources/views/bookingsmanage.blade.php --}}
@extends('layouts.app')

@section('title', 'คำขออนุมัติการจอง')

@section('content')
<h2>คำขออนุมัติการจอง</h2>

@if(session('success'))
    <div class="alert alert-success mt-3">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered mt-4 align-middle">
    <thead class="table-warning text-center">
        <tr>
        <th>ล็อก</th>
        <th>เดือน / ปี</th>
        <th>ชื่อ - นามสกุล</th>
        <th>ชื่อร้าน</th>
        <th>หลักฐานการโอน</th>
        <th>คำขออนุมัติ</th>
        </tr>
    </thead>
    <tbody>
        @foreach($bookings as $bk)
        @if($bk->user)
            @php
            // สลิปอันล่าสุด (ถ้า payments เป็น hasMany)
            $payment = optional($bk->payments)->last();

            // ถ้าเก็บไฟล์ใน storage disk 'public' เช่น store('slips','public')
            $slipUrl = $payment && $payment->slip_path
                ? asset('storage/'.$payment->slip_path)
                : null;

            // ชื่อ-สกุล (เผื่อคอลัมน์ต่างกันระหว่าง db)
            $fname = $bk->user->users_fname ?? $bk->user->Users_Fname ?? $bk->user->name ?? '-';
            $lname = $bk->user->users_lname ?? $bk->user->Users_Lname ?? '';
            // ชื่อร้านจากความสัมพันธ์ user->shopDetail
            $shopName = $bk->user->shopDetail->shop_name ?? '-';
            @endphp

            <tr>
            <td>ล็อก {{ $bk->stall->stall_code ?? $bk->stall->code ?? '-' }}</td>
            <td>{{ $bk->month }}/{{ $bk->year }}</td>
            <td>{{ $fname }} {{ $lname }}</td>
            <td>{{ $bk->user->shopDetail->shop_name ?? '-' }}</td>
            <td class="text-center">
                @if($slipUrl)
                <a href="{{ $slipUrl }}" target="_blank">
                    <img src="{{ $slipUrl }}" width="90" class="border rounded">
                </a>
                @else
                <span class="text-muted">- ไม่มีข้อมูล -</span>
                @endif
            </td>

            <td class="text-center">
                <a href="{{ route('admin.booking.approve', ['id' => $bk->booking_id]) }}"
                class="btn btn-success btn-sm"
                onclick="return confirm('ยืนยันการอนุมัติการจองนี้หรือไม่?');">Approve</a>

                <a href="{{ route('admin.booking.cancel', ['id' => $bk->booking_id]) }}"
                class="btn btn-danger btn-sm"
                onclick="return confirm('ยืนยันการไม่อนุมัติการจองนี้หรือไม่?');">Cancel</a>
            </td>
            </tr>
        @endif
        @endforeach
    </tbody>
    </table>
@endsection


</body>
</html>