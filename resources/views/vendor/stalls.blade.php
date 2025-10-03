@extends('layouts.app_vendor')

@section('title', 'เลือกล็อก')

@section('content')
    {{-- Flash --}}
    @if (session('ok'))
        <div class="alert ok">{{ session('ok') }}</div>
        @endif
        @if ($errors->any())
        <div class="alert err">
            @foreach($errors->all() as $e) <div>{{ $e }}</div> @endforeach
        </div>
        @endif

        {{-- ฟอร์มเลือกเดือน (รับ ค.ศ. ตรง ๆ) --}}
        <form method="GET" action="{{ route('vendor.stalls') }}" class="month-form">
        <label>เดือน :</label>

        <select name="month">
            @foreach (range(1,12) as $mm)
            <option value="{{ $mm }}" {{ $mm == $m ? 'selected' : '' }}>
                {{ \Carbon\Carbon::create(null,$mm)->locale('th')->translatedFormat('F') }}
            </option>
            @endforeach
        </select>

        <input type="number" name="year" value="{{ $y }}" min="2000" max="2100">
        <button type="submit">แสดง</button>

        <div class="legend-wrap">
            <span class="legend lg-available">ว่าง</span>
            <span class="legend lg-unavailable">ไม่ว่าง</span>
            <span class="legend lg-pending">รออนุมัติ</span>
            <span class="legend lg-closed">ปิดให้จอง</span>
        </div>
        </form>

        {{-- กริดล็อก แบ่งตามโซน --}}
        <section class="zones">
        @foreach ($stalls->groupBy(fn($row)=>$row['stall']->zone->zone_name) as $zoneName => $rows)
            <article class="zone-card">
            <h3>{{ $zoneName }}</h3>

            <div class="grid-stalls">
                @foreach ($rows as $row)
                @php
                    $stall = $row['stall'];
                    $sid   = $row['status_id'];

                    $btnClass = match($sid){
                    \App\Models\Status::AVAILABLE   => 'btn-available',
                    \App\Models\Status::UNAVAILABLE => 'btn-unavailable',
                    \App\Models\Status::PENDING     => 'btn-pending',
                    \App\Models\Status::CLOSED      => 'btn-closed',
                    };
                    $disabled = in_array($sid, [
                    \App\Models\Status::UNAVAILABLE,
                    \App\Models\Status::PENDING,
                    \App\Models\Status::CLOSED
                    ]);
                @endphp

                <form method="POST"
                        action="{{ route('vendor.stall.book', $stall->stall_id) }}"
                        class="stall-form">
                    @csrf
                    <input type="hidden" name="year"  value="{{ $y }}">
                    <input type="hidden" name="month" value="{{ $m }}">

                    <button type="submit"
                            class="btn-stall {{ $btnClass }}"
                            title="สถานะ: {{ $row['status_name'] }}"
                            @if($disabled) disabled @endif>
                    {{ $stall->stall_code }}
                    </button>
                </form>
                @endforeach
            </div>
            </article>
        @endforeach
    </section>

@endsection
