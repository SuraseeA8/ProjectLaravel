@extends('layouts.app')

@section('title', 'รายละเอียดล็อก')

@section('content')
    <main class="container">

        {{-- Flash / Error --}}
        @php
            $sid = $monthStatus->status_id ?? \App\Models\Status::AVAILABLE;
            $statusName = match ($sid) {
                \App\Models\Status::AVAILABLE => 'ว่าง',
                \App\Models\Status::UNAVAILABLE => 'ไม่ว่าง',
                \App\Models\Status::PENDING => 'รออนุมัติ',
                \App\Models\Status::CLOSED => 'ปิดให้จอง',
                default => 'ไม่ทราบสถานะ',
            };
            $statusClass = match ($sid) {
                \App\Models\Status::AVAILABLE => 'badge-available',
                \App\Models\Status::UNAVAILABLE => 'badge-unavailable',
                \App\Models\Status::PENDING => 'badge-pending',
                \App\Models\Status::CLOSED => 'badge-closed',
                default => 'badge-neutral',
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

        {{-- ปุ่มย้อนกลับ + ปุ่มจอง --}}
        <section class="action-row">
            <a href="{{ route('vendor.stalls', ['year' => $y, 'month' => $m]) }}" class="btn back-btn">
                ← กลับไปหน้าล็อก
            </a>

            @if ($canBook)
                <a href="{{ route('vendor.stall.checkout', $stall->stall_id) }}?year={{ $y }}&month={{ $m }}"
                    class="btn btn-primary">จองล็อกนี้</a>
            @else
                <button type="button" class="btn btn-secondary" disabled>จองล็อกนี้</button>
                <div class="text-danger mt-2">{{ $cannotReason }}</div>
            @endif
        </section>
    </main>
@endsection

<style>
    body {
        background: #fffde7;
        font-family: 'Kanit', sans-serif;
    }

    main.container {
        max-width: 900px;
        margin: 30px auto;
        padding: 20px;
    }

    /* Header */
    header.mb-3 {
        text-align: center;
        padding: 20px;
        background: #fff;
        border: 2px solid #E68F36;
        border-radius: 12px;
        margin-bottom: 25px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
    }

    header.mb-3 h2 {
        font-size: 26px;
        font-weight: bold;
        color: #E68F36;
        margin-bottom: 8px;
    }

    header.mb-3 .text-sm {
        font-size: 14px;
        color: #666;
    }

    /* การ์ด */
    .card {
        background: #fff;
        border: 2px solid #E68F36;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
    }

    .card p {
        font-size: 15px;
        margin: 6px 0;
    }

    /* Badge */
    .badge {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: bold;
        color: #fff;
    }

    .badge-available {
        background: #4CAF50;
    }

    .badge-unavailable {
        background: #f44336;
    }

    .badge-pending {
        background: #1976D2;
    }

    .badge-closed {
        background: #9e9e9e;
    }

    .badge-neutral {
        background: #ccc;
    }

    /* ปุ่ม */
    .btn {
        display: inline-block;
        padding: 10px 20px;
        border-radius: 25px;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: 0.3s;
    }

    .btn-primary {
        background: #E68F36;
        color: #fff;
    }

    .btn-primary:hover {
        background: #d87c2e;
    }

    .btn-secondary {
        background: #ccc;
        color: #444;
        cursor: not-allowed;
    }

    .btn:hover:not([disabled]) {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    /* ปุ่มย้อนกลับ + ปุ่มจอง อยู่บรรทัดเดียว ตรงกลาง */
    .action-row {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-top: 30px;
        align-items: center;
    }

    .back-btn {
        background: #fff;
        border: 1px solid #ccc;
        color: #333;
    }

    .back-btn:hover {
        background: #f9f9f9;
    }

    /* Flash */
    .alert.ok {
        background: #e8f5e9;
        border: 1px solid #4CAF50;
        color: #2e7d32;
        padding: 10px;
        border-radius: 10px;
        text-align: center;
        margin-bottom: 15px;
    }

    .alert.err {
        background: #ffebee;
        border: 1px solid #f44336;
        color: #b71c1c;
        padding: 10px;
        border-radius: 10px;
        margin-bottom: 15px;
    }

    .text-danger {
        font-size: 14px;
        color: #c62828 !important;
        text-align: center;
    }
</style>