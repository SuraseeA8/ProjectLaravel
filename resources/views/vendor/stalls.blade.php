@extends('layouts.app_vendor')

@section('title', 'เลือกล็อก')

@section('content')
    <div class="container vendor-stalls"
            data-month="{{ $month }}"
            data-year="{{ $year }}"
            data-detail-url-template="{{ route('vendor.stall.detail', ['id' => 'STALL_ID', 'month' => 'MM', 'year' => 'YYYY']) }}">
        <h2 class="page-title">เลือกล็อกที่ต้องการจอง</h2>

        <!-- ตัวเลือกเดือน -->
        <div class="filter-bar">
            <label for="month">เดือน :</label>
            <input type="month" id="month"
                value="{{ sprintf('%04d-%02d', $year, $month) }}">
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
            @foreach($zones as $zone)
                <section class="zone" aria-label="โซน {{ $zone->zone_name }}">
                    <h3>{{ $zone->zone_name }}</h3>
                    <div class="stall-grid">
                    @foreach($zone->stalls as $stall)
                        @php $stt = (int)($stall->current_status_id ?? 1); @endphp
                        <a class="stall status-{{ $stt }}"
                            data-stall-id="{{ $stall->stall_id }}"
                            href="{{ route('vendor.stall.detail', ['id'=>$stall->stall_id,'month'=>$month,'year'=>$year]) }}"
                            title="ล็อก {{ $stall->code }}">
                            {{ $stall->code }}
                        </a>
                    @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    </div>
@endsection
