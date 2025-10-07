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
    @extends('layouts.main')

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
                        <tr>
                            <td>ล็อก {{ $bk->stall->stall_code ?? '-' }}</td>
                            <td>{{ $bk->month }}/{{ $bk->year }}</td>
                            <td>{{ $bk->user->users_fname ?? '-' }} {{ $bk->user->users_lname ?? '' }}</td>

                            {{-- ชื่อร้าน --}}
                            @php

                                $shopName = '-';
                                foreach($shops as $sp) {
                                    if($sp->User_id == $bk->user->User_id) {
                                        $shopName = $sp->shop_name;
                                        break;
                                    }
                                }
                            @endphp
                            <td>{{ $shopName }}</td>
                            @php
                                // หา payment ล่าสุด (ถ้ามี)
                                $payment = optional($bk->payments)->last();
                                $slipUrl = $payment && $payment->slip_path
                                ? asset('storage/'.$payment->slip_path)
                                : null;
                            @endphp
                            {{-- หลักฐานการโอน --}}
                            <td class="text-center">
                                @if($slipUrl)
                                <a href="{{ $slipUrl }}" target="_blank">
                                    <img src="{{ $slipUrl }}" width="90" class="border rounded">
                                </a>
                                @else
                                <span class="text-muted">- ไม่มีข้อมูล -</span>
                                @endif
                            </td>

                            {{-- ปุ่มอนุมัติ --}}
                            <td class="text-center">
                                <a href="{{ route('admin.booking.approve', ['id' => $bk->booking_id]) }}"
                                class="btn btn-success btn-sm"
                                onclick="return confirm('ยืนยันการอนุมัติการจองนี้หรือไม่?');">Approve</a>

                                <a href="{{ route('admin.booking.cancel', ['id' => $bk->booking_id]) }}"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('ยืนยันการไม่อนุมัติการจองนี้หรือไม่?');">Cancle</a>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    @endsection
</body>
</html>