@extends('layouts.app')

    @section('title', 'คำขออนุมัติการจอง')

    @section('content')
        <h2 class="text-center">คำขออนุมัติการจอง</h2>

        @if(session('success'))
            <div class="alert alert-success mt-3">{{ session('success') }}</div>
        @endif

        <table class="table table-striped mt-4 align-middle">
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
                                onclick="return confirm('ยืนยันการอนุมัติการจองนี้หรือไม่?');">อนุมัติ</a>

                                <a href="{{ route('admin.booking.cancel', ['id' => $bk->booking_id]) }}"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('ยืนยันการยกเลิกการจองนี้หรือไม่?');">ยกเลิก</a>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    @endsection
<style>
    /* ===== Theme ===== */
    :root{
    --brand:#E68F36;
    --brand-2:#d87c2e;
    --bg-soft:#fffde7;
    --card:#ffffff;
    --line:#f0d9c3;
    --text:#333;
    }

    body{
    background:var(--bg-soft);
    font-family:'Kanit', sans-serif;
    }

    /* ===== จัดหน้าให้อยู่กึ่งกลางทั้งหมด โดยไม่แก้ HTML ===== */
    main{
    display:flex;
    justify-content:center;   /* จัดกลางแนวนอน */
    padding:24px 16px;
    }
    main > *{
    width:100%;
    max-width:1200px;         /* กรอบเนื้อหา */
    margin-left:auto;
    margin-right:auto;
    }

    /* ===== หัวข้อ ===== */
    h2.text-center{
    text-align: center;
    color:var(--brand);
    font-weight:700;
    margin-bottom:10px;
    }

    /* ===== Alert ให้อยู่กึ่งกลาง และกว้างเท่ากับคอนเทนต์ ===== */
    .alert{
    max-width:1200px;
    margin:12px auto 0;
    border-radius:10px;
    }
    .alert-success{
    border:1.5px solid #A5D6A7;
    }

    /* ===== ตาราง (อยู่กลาง + ธีมเดียวกัน) ===== */
    .table{
    background:#fff;
    overflow:hidden;                 /* โค้งหัวตารางเนียน */
    box-shadow:0 6px 16px rgba(0,0,0,.08);
    margin:16px auto 0;              /* กึ่งกลาง */
    width:100%;
    max-width:1200px;                /* ความกว้างสูงสุด */
    }

    .table thead.table-warning{
    background:linear-gradient(90deg, var(--brand), var(--brand-2)) !important;
    color:#fff;
    }
    .table thead th{
    text-align:center;
    font-weight:600;
    padding:14px 12px;
    border-bottom:none !important;
    white-space:nowrap;
    }

    .table tbody td{
    text-align:center;
    color:var(--text);
    vertical-align:middle;
    border-color:var(--line) !important;
    padding:12px 10px;
    background:#fff;
    }

    /* สลับสีแถว (นิ่ง ไม่ต้อง hover) */
    .table.table-striped > tbody > tr:nth-of-type(even) > *{
    background:#FFF9F3 !important;
    }
    .table tbody tr:hover > *{
    background:inherit !important;   /* ไม่ให้ไล่สีเวลา hover */
    }

    /* รูปสลิปให้อยู่กลางและมีกรอบนุ่ม ๆ */
    .table td img{
    display:inline-block;
    max-width:90px;
    height:auto;
    border:1px solid var(--line);
    border-radius:8px;
    }

    .btn, .btn a {
        text-decoration: none !important;  /* ตัดเส้นใต้ */
    }


    /* ปุ่ม */
    .btn{
    color: white;
    border:none;
    border-radius:10px;
    padding:6px 12px;
    }
    .btn-success{ background:#4CAF50; }
    .btn-success:hover{ background:#388E3C; }
    .btn-danger{ background:#f44336; }
    .btn-danger:hover{ background:#d32f2f; }


</style>



