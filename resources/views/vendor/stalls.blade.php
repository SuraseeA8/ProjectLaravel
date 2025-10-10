@extends('layouts.app')

@section('title', 'เลือกล็อก')

@section('content')


    {{-- ฟอร์มเลือกเดือน (รับ ค.ศ. ตรง ๆ) --}}
    <form method="GET" action="{{ route('vendor.stalls') }}" class="month-form">
        <label>เดือน :</label>
        <select name="month">
            @foreach (range($startMonth, 12) as $mm)
                <option value="{{ $mm }}" {{ (int) $mm === (int) $m ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::createFromDate($y, $mm, 1)->locale('th')->translatedFormat('F') }}
                </option>
            @endforeach
        </select>

        <input type="number" name="year" value="{{ $y }}" min="2025" max="2100">
        <button type="submit">แสดง</button>
    </form>


    {{-- กริดล็อก แบ่งตามโซน --}}
    <section class="zones">
        <h2>เลือกล็อกที่ต้องการจอง</h2>
        <div class="legend-wrap">
            <span class="legend lg-available">ว่าง</span>
            <span class="legend lg-unavailable">ไม่ว่าง</span>
            <span class="legend lg-pending">รออนุมัติ</span>
            <span class="legend lg-closed">ปิดให้จอง</span>
        </div>
        @foreach ($stalls->groupBy(fn($row) => $row['stall']->zone->zone_name) as $zoneName => $rows)
            <article class="zone-card">
                <h3>{{ $zoneName }}</h3>

                <div class="grid-stalls">
                    @foreach ($rows as $row)
                        @php
                            $stall = $row['stall'];
                            $sid = (int) $row['status_id']; 
                            $btnClass = match ($sid) {
                                \App\Models\Status::AVAILABLE => 'btn-available',
                                \App\Models\Status::UNAVAILABLE => 'btn-unavailable',
                                \App\Models\Status::PENDING => 'btn-pending',
                                \App\Models\Status::CLOSED => 'btn-closed',
                                \App\Models\Status::CANCEL => 'btn-cancel', 
                            };
                            $disabled = in_array($sid, [
                                \App\Models\Status::UNAVAILABLE,
                                \App\Models\Status::PENDING,
                                \App\Models\Status::CLOSED,
                            ]);
                        @endphp

                        <a href="{{ route('vendor.stall.detail', $stall->stall_id) }}?year={{ $y }}&month={{ $m }}"
                            class="btn-stall {{ $btnClass }}" title="สถานะ: {{ $row['status_name'] }}"
                            aria-label="ล็อก {{ $stall->stall_code }} - {{ $row['status_name'] }}">
                            {{ $stall->stall_code }}
                        </a>
                    @endforeach
                </div>
            </article>
        @endforeach
    </section>
@endsection

<style>
    body {
        background: #fffde7;
        /* พื้นหลังเหลืองอ่อน */
        font-family: 'Kanit', sans-serif;
    }

    h2 {
        text-align: center;
    }

    /* ฟอร์มเลือกเดือน */
    .month-form {
        text-align: center;
        margin: 20px auto;
    }

    .month-form label {
        font-weight: bold;
        margin-right: 6px;
        font-size: 18px;
    }

    .month-form select,
    .month-form input[type="number"],
    .month-form button {
        padding: 6px 12px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 15px;
        margin: 0 5px;
    }

    .month-form button {
        background: #E68F36;
        color: white;
        cursor: pointer;
    }

    .month-form button:hover {
        background: #cf7b2d;
    }

    /* legend */
    .legend-wrap {
        margin-top: 15px;
        display: flex;
        justify-content: center;
        gap: 20px;
        font-size: 15px;
        font-weight: bold;
    }

    .legend::before {
        content: "";
        display: inline-block;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        margin-right: 6px;
    }

    .lg-available::before {
        background: #4caf50;
    }

    .lg-unavailable::before {
        background: #e68f36;
    }

    .lg-pending::before {
        background: #1e88e5;
    }

    .lg-closed::before {
        background: #999;
    }

    /* โซน */
    .zones {
        max-width: 950px;
        margin: 25px auto;
        padding: 20px;
        border: 2px solid #E68F36;
        border-radius: 16px;
        background: #fff;
    }

    .zones h3 {
        text-align: center;
        font-size: 20px;
        margin-bottom: 15px;
        color: #333;
    }

    .zone-card {
        margin: 20px;
        border: 2px solid #E68F36;
        border-radius: 12px;
        padding: 15px;
        display: inline-block;
        vertical-align: top;
        width: 28%;
        background: #fffef7;
    }

    /* กริดล็อก */
    .grid-stalls {
        display: grid;
        grid-template-columns: repeat(2, 70px);
        gap: 15px 30px;
        justify-content: center;
    }

    /* ปุ่มล็อก */
    .btn-stall {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 60px;
        height: 50px;
        border-radius: 10px;
        font-weight: bold;
        font-size: 14px;
        text-decoration: none;
        background: #e0e0e0;
        border: 1px solid #ccc;
        color: #333;
        transition: 0.2s;
    }

    .btn-stall:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    /* สถานะสี */
    .btn-available {
        background: #4caf50;
        color: white;
    }

    .btn-unavailable {
        background: #e68f36;
        color: white;
    }

    .btn-pending {
        background: #1e88e5;
        color: white;
    }

    .btn-closed {
        background: #bdbdbd;
        color: white;
    }
</style>
