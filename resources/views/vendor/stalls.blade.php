@extends('layouts.app_vendor')

@section('title', 'เลือกล็อก')
@section('content')
<div class="container">
    <h2 class="page-title">เลือกล็อกที่ต้องการจอง</h2>

    <!-- ตัวเลือกเดือน -->
    <div class="filter-bar">
        <label for="month">เดือน :</label>
        <input type="month" id="month" value="{{ date('Y-m') }}">
    </div>

    <!-- Legend -->
    <div class="legend">
        <span><span class="dot available"></span> ว่าง</span>
        <span><span class="dot booked"></span> ไม่ว่าง</span>
        <span><span class="dot pending"></span> รออนุมัติ</span>
        <span><span class="dot closed"></span> ปิดให้จอง</span>
    </div>

    <!-- แผนผังโซน -->
    <div class="zones">
        @foreach($stalls->groupBy('zone.zone_name') as $zoneName => $zoneStalls)
            <div class="zone">
                <h3>{{ $zoneName }}</h3>
                <div class="stall-grid">
                    @foreach($zoneStalls as $stall)
                        <a href="{{ route('vendor.stall.detail', $stall->stall_id) }}"  class="stall"  data-id="{{ $stall->stall_id }}"  data-status="{{ $stall->status }}">
                            {{ $stall->stall_code }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
