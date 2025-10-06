@extends('layouts.app_vendor')

@section('title', 'รายละเอียดล็อก')

@section('content')
    <main class="container">

        {{-- Flash / Error --}}
        @if (session('ok'))
            <div class="alert ok">{{ session('ok') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert err">
                @foreach($errors->all() as $e) 
                    <div>{{ $e }}</div> 
                @endforeach
            </div>
        @endif

        @php
            // สรุปสถานะเดือนนี้ของล็อกนี้
            $sid = $monthStatus->status_id ?? \App\Models\Status::AVAILABLE;
            $statusName = match ($sid) {
            \App\Models\Status::AVAILABLE   => 'ว่าง',
            \App\Models\Status::UNAVAILABLE => 'ไม่ว่าง',
            \App\Models\Status::PENDING     => 'รออนุมัติ',
            \App\Models\Status::CLOSED      => 'ปิดให้จอง',
            default                         => 'ไม่ทราบสถานะ',
            };
            $statusClass = match ($sid) {
            \App\Models\Status::AVAILABLE   => 'badge-available',
            \App\Models\Status::UNAVAILABLE => 'badge-unavailable',
            \App\Models\Status::PENDING     => 'badge-pending',
            \App\Models\Status::CLOSED      => 'badge-closed',
            default                         => 'badge-neutral',
            };
        @endphp

        <header class="mb-3">
            <h2 class="mb-1">รายละเอียดล็อก {{ $stall->stall_code }}</h2>
            <div class="text-sm text-muted">
            เดือนที่ดู: {{ \Carbon\Carbon::create($y, $m)->locale('th')->translatedFormat('F Y') }}
            </div>
        </header>

        <section class="card p-3 mb-4">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>โซน:</strong> {{ optional($stall->zone)->zone_name ?? '-' }}</p>
                    <p><strong>ขนาด:</strong> {{ $stall->size ?: '-' }}</p>
                    <p><strong>สถานที่:</strong> {{ $stall->location ?: '-' }}</p>
                    <p><strong>เวลาเปิด-ปิด:</strong> {{ $stall->stall_condition ?: '-' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>ราคา:</strong> {{ number_format($stall->price, 2) }} บาท</p>
                    <p><strong>ค่าน้ำ:</strong> {{ number_format($stall->water_fee, 2) }} บาท</p>
                    <p><strong>ค่าไฟ:</strong> {{ number_format($stall->electric_fee, 2) }} บาท</p>
                    <p><strong>สถานะเดือนนี้:</strong>
                    <span class="badge {{ $statusClass }}">{{ $statusName }}</span>
                    </p>
                </div>
            </div>
        </section>

        {{-- ปุ่มจอง / เหตุผลที่จองไม่ได้ --}}
        <section class="mb-4">
            @if ($canBook)
                <a href="{{ route('vendor.stall.checkout', $stall->stall_id) }}?year={{ $y }}&month={{ $m }}"class="btn btn-primary">
                    จองล็อกนี้
                </a>

            @else
                <button type="button" class="btn btn-secondary" disabled>จองล็อกนี้</button>
                <div class="text-danger mt-2">{{ $cannotReason }}</div>
            @endif
        </section>


        <a href="{{ route('vendor.stalls', ['year' => $y, 'month' => $m]) }}" class="btn">
            ← กลับไปหน้าล็อก
        </a>
    </main>
@endsection
