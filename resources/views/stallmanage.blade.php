<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>การจัดการล็อกตลาด</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat">

    <style>
        body {
            background-color: #fff7dc;
            font-family: "Montserrat", sans-serif; 
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .container {
            max-width: 900px;
            background: #fff;
            padding: 25px 40px;
            border-radius: 15px;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
        }
        .legend {
            display: flex;
            justify-content: center;
            gap: 25px;
            margin: 15px 0 30px 0;
            font-size: 15px;
        }
        .legend span {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .legend .dot {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            display: inline-block;
        }
        .dot.available { background: #28a745; }
        .dot.not-available { background: #ff9800; }
        .dot.pending { background: #2196f3; }
        .dot.closed { background: #9e9e9e; }

        .zone-wrapper {
            display: flex;
            justify-content: center;
            gap: 30px;
        }
        .zone-box {
            border: 2px solid #ff9800;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            width: 230px;
            background-color: #fffef8;
        }
        .zone-box h5 {
            font-weight: bold;
            margin-bottom: 15px;
        }
        .stall-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
        .stall-btn {
            width: 100%;
            padding: 8px;
            border-radius: 8px;
            border: none;
            color: #fff;
            font-weight: 500;
            transition: 0.2s;
        }
        .stall-btn.available { background: #28a745; }      /* ว่าง */
        .stall-btn.not-available { background: #ff9800; }  /* ไม่ว่าง */
        .stall-btn.pending { background: #2196f3; }        /* รออนุมัติ */
        .stall-btn.closed { background: #9e9e9e; }         /* ปิดใช้งาน */
        .stall-btn:hover { opacity: 0.8; }
    </style>
</head>
<body class="p-4">
@extends('layouts.app')

@section('title', 'การจัดการล็อกตลาด')

@section('content')
    <div class="container">
        <h2>เลือกล็อกที่ต้องจัดการ</h2>

        
        <form method="GET" action="{{ route('admin.stalls.index') }}" class="d-flex justify-content-center mb-3" style="gap:10px;">
            <input type="number" name="month" class="form-control w-25" placeholder="เดือน" min="1" max="12" value="{{ $month }}">
            <input type="number" name="year" class="form-control w-25" placeholder="ปี" value="{{ $year }}">
            <button type="submit" class="btn btn-primary">ดูข้อมูล</button>
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
                                // ตรวจสถานะล็อก
                                $status_id = null;
                                foreach($stallStatuses as $stt) {
                                    if($stt->stall_id == $stall->stall_id && $stt->month == $month && $stt->year == $year) {
                                        $status_id = $stt->status_id;
                                        break;
                                    }
                                }

                                
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
</body>
</html>