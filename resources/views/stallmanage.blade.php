@extends('layouts.app')

@section('title', 'การจัดการล็อกตลาด')

@section('content')
    <div class="container">
        <h2>เลือกล็อกที่ต้องจัดการ</h2>

        @php
            // กำหนดค่าเริ่มต้นเผื่อไม่ได้ส่งมา
            $m = isset($month) ? (int)$month : (int)request('month', now('Asia/Bangkok')->month);
            $y = isset($year)  ? (int)$year  : (int)request('year',  now('Asia/Bangkok')->year);
        @endphp

        <form method="GET" action="{{ route('admin.stalls.index') }}" class="month-form">
            <label>เดือน :</label>
            <select name="month">
            @foreach (range(1, 12) as $mm)
                <option value="{{ $mm }}" {{ $mm == $m ? 'selected' : '' }}>
                {{ \Carbon\Carbon::create(null, $mm, 1)->locale('th')->translatedFormat('F') }}
                </option>
            @endforeach
            </select>

            <input type="number" name="year" value="{{ $y }}" min="2025" max="2100">
            <button type="submit">ดูข้อมูล</button>
        </form>

        <div class="legend">
            <span><span class="dot available"></span> ว่าง</span>
            <span><span class="dot not-available"></span> ไม่ว่าง</span>
            <span><span class="dot pending"></span> รออนุมัติ</span>
            <span><span class="dot closed"></span> ปิดใช้งาน</span>
        </div>

        {{-- โซนทั้งหมด --}}
        <div class="zone-wrapper">
            @foreach([1 => 'A', 2 => 'B', 3 => 'C'] as $zoneId => $zoneName)
                <div class="zone-box">
                    <h5>โซน {{ $zoneName }}</h5>
                    <div class="stall-grid">
                        @foreach($stalls->where('zone_id', $zoneId) as $stall)
                            @php
                                // ตรวจสถานะล็อกของเดือน/ปีที่เลือก
                                $status_id = null;
                                foreach($stallStatuses as $stt) {
                                    if ($stt->stall_id == $stall->stall_id && $stt->month == $month && $stt->year == $year) {
                                        $status_id = $stt->status_id;
                                        break;
                                    }
                                }
                                // map class ตามสถานะ (คง logic เดิม)
                                $statusClass = match($status_id) {
                                    1 => 'available',
                                    2 => 'not-available',
                                    3 => 'pending',
                                    5 => 'closed',
                                    default => 'available'
                                };
                            @endphp

                            <form action="{{ route('admin.stalls.toggle', [$stall->stall_id, $month, $year]) }}" method="GET">
                                <button type="submit"
                                        class="stall-btn {{ $statusClass }}"
                                        onclick="return confirm('ยืนยันการเปลี่ยนสถานะล็อก {{ $stall->stall_code }} หรือไม่?');">
                                    {{ $stall->stall_code }}
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

    {{-- ใส่ CSS ไว้ในไฟล์นี้เลยตามที่ต้องการ --}}
    <style>
    /* โทน/ฟอนต์แบบหน้า vendor */
    :root{
        --brand:#E68F36; --bg:#fffde7; --card:#fff; --line:#f0d9c3; --text:#222;
    }
    body{ background:var(--bg); font-family:'Kanit', sans-serif; color:var(--text); }

    /* กล่องใหญ่ */
    .container{
        max-width: 950px;
        margin: 25px auto;
        padding: 20px;
        border: 2px solid var(--brand);
        border-radius: 16px;
        background: var(--card);
        box-shadow: 0 6px 16px rgba(0,0,0,.08);
    }
    h2{
        text-align:center;
        font-size:24px;
        font-weight:700;
        color:var(--brand);
        margin-bottom: 10px;
    }

    /* ฟอร์มเลือกเดือนแบบ vendor */
    .month-form{ text-align:center; margin: 20px auto; }
    .month-form label{ font-weight:700; margin-right:6px; font-size:18px; }
    .month-form select,
    .month-form input[type="number"],
    .month-form button{
        padding:6px 12px; border:1px solid #ccc; border-radius:8px;
        font-size:15px; margin:0 5px; background:#fff;
    }
    .month-form button{
        background:var(--brand); color:#fff; cursor:pointer;
        border-color: transparent;
    }
    .month-form button:hover{ background:#cf7b2d; }

    /* legend ให้เหมือน vendor */
    .legend{
        margin-top: 10px; margin-bottom: 18px;
        display:flex; justify-content:center; gap:20px; font-size:15px; font-weight:700;
    }
    .legend span{ display:inline-flex; align-items:center; gap:8px; }
    .legend .dot{ width:14px; height:14px; border-radius:50%; display:inline-block; }
    .legend .available{ background:#4caf50; }
    .legend .not-available{ background:#e68f36; }
    .legend .pending{ background:#1e88e5; }
    .legend .closed{ background:#999; }

    /* โซน → ให้หน้าตาเป็น “การ์ด” แบบ vendor */
    .zone-wrapper{
        display:flex; flex-wrap:wrap; justify-content:center; gap:20px;
    }
    .zone-box{
        border: 2px solid var(--brand);
        border-radius: 12px;
        padding: 15px;
        width: 280px;
        background: #fffef7;
    }
    .zone-box h5{
        text-align:center; font-size:20px; margin:0 0 12px 0; color:#333; font-weight:700;
    }

    /* กริดล็อกแบบ vendor */
    .stall-grid{
        display:grid;
        grid-template-columns: repeat(2, 70px);
        gap: 15px 30px;
        justify-content: center;
    }

    /* ปุ่มล็อกให้เหมือน .btn-stall ของ vendor */
    .stall-btn{
        display:flex; align-items:center; justify-content:center;
        width: 60px; height: 50px; border-radius: 10px;
        font-weight:700; font-size:14px; text-decoration:none;
        border:1px solid #ccc; color:#fff; transition:.2s;
    }
    .stall-btn:hover{ transform: scale(1.05); box-shadow:0 4px 10px rgba(0,0,0,.2); }

    /* สีตามสถานะ (ชื่อคลาสคุณเดิม) */
    .stall-btn.available      { background:#4caf50; }
    .stall-btn.not-available  { background:#e68f36; }
    .stall-btn.pending        { background:#1e88e5; }
    .stall-btn.closed         { background:#bdbdbd; color:#fff; }
</style>
