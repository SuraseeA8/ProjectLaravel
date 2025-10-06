<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>หน้าแรก - ระบบจองล็อกตลาดนัด</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit&display=swap" rel="stylesheet">
    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            font-family: 'Kanit', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header {
            background: #E68F36;
            padding: 10px 50px;
        }

        .navbar-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            color: white;
            margin: 0;
        }

        nav ul {
            background-color: #FFFFCC;
            list-style: none;
            display: flex;
            gap: 20px;
            margin: 5px;
            padding: 10px 20px;
            border-radius: 50px;
        }

        .home a {
            color: #E68F36;
        }

        nav ul li a {
            color: black;
            text-decoration: none;
        }

        nav ul li.drop {
            position: relative;
            /* ให้ li เป็นตัวอ้างอิง */
        }

        /* เมนูย่อย */
        nav ul li.drop ul.down {
            display: none;
            /* ซ่อนก่อน */
            position: absolute;
            top: 100%;
            left: 0;
            min-width: 180px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            list-style: none;
            margin: 0;
            padding: 5px 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 1000;
        }

        /* แสดงเมื่อ hover */
        nav ul li.drop:hover>ul.down {
            display: block;
        }

        /* รายการภายใน */
        nav ul li.drop ul.down li {
            width: 100%;
        }

        nav ul li.drop ul.down li a,
        nav ul li.drop ul.down li button {
            display: block;
            padding: 10px 15px;
            color: #333;
            text-decoration: none;
            font-size: 14px;
            background: none;
            border: none;
            text-align: left;
            cursor: pointer;
            width: 100%;
        }

        /* hover effect */
        nav ul li.drop ul.down li a:hover,
        nav ul li.drop ul.down li button:hover {
            background: #f1f1f1;
            color: #E68F36;
        }

        footer {
            background: #E68F36;
            color: #fff;
            text-align: center;
            padding: 15px;
        }

        /* ตารางรายงาน */
        .report-card {
            margin-top: 20px;
            padding: 15px;
            border: 2px solid #E68F36;
            border-radius: 15px;
            background: #fff;
            max-width: 90%;
            margin-left: auto;
            margin-right: auto;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
        }

        .report-card h2 {
            text-align: center;
            margin-bottom: 15px;
            color: #333;
        }

        .report-table {
            border-collapse: collapse;
            width: 100%;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
        }

        .report-table th {
            background: #FFF3E0;
            border: 1px solid #E68F36;
            padding: 10px;
            text-align: center;
            color: #333;
        }

        .report-table td {
            border: 1px solid #E68F36;
            padding: 10px;
            text-align: center;
        }

        /* กล่องสรุปผล */
        .report-summary {
            margin-top: 30px;
            padding: 15px 20px;
            border: 2px solid #E68F36;
            border-radius: 15px;
            background: #fff;
            max-width: 350px;
            margin-left: auto;
            margin-right: auto;
            text-align: center;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
        }

        .report-summary h3 {
            margin-bottom: 15px;
            color: #333;
        }

        .report-summary p {
            margin: 8px 0;
            font-size: 16px;
        }

        /* เน้นตัวเลข */
        .report-summary span {
            display: inline-block;
            background: #FFF3E0;
            border: 2px solid #E68F36;
            padding: 5px 15px;
            border-radius: 20px;
            margin-left: 5px;
            font-weight: bold;
            color: #333;
        }

        .report-header {
            text-align: center;
            margin: 20px auto;
        }

        .report-header form {
            margin-top: 10px;
        }
    </style>

<body>
    <header>
        <div class="navbar-container">
            <h1 class="logo">Market Booking</h1>
            <!-- Navbar -->
            <nav>
                <ul>
                    {{-- Guest --}}
                    @guest
                        <li class="home"><a href="{{ route('index') }}">หน้าแรก</a></li>
                        <li><a href="{{ route('public.board') }}">ประกาศ</a></li>
                        <li><a href="#">ติดต่อเรา</a></li>
                        <li><a href="{{ route('login') }}">เข้าสู่ระบบ</a></li>
                        <li><a href="{{ route('register') }}">ลงทะเบียน</a></li>
                    @endguest

                    {{-- Auth --}}
                    @auth
                        @if(Auth::user()->role_id == 2)
                            <li class="home"><a href="{{ route('index') }}">หน้าแรก</a></li>
                            <li><a href="{{ route('vendor.events') }}">ประกาศ</a></li>
                            <li><a href="#">จองล็อก</a></li>
                            <li><a href="#">รายการจอง</a></li>
                            <li><a href="#">ติดต่อเรา</a></li>
                            <li class="drop">บัญชีผู้ใช้
                                <ul class="down">
                                    <li><a href="#">แก้ไขบัญชีผู้ใช้</a></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" style="background:none;border:none;cursor:pointer;">
                                                ออกจากระบบ
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endif

                        @if(Auth::user()->role_id == 1)
                            <li class="home"><a href="{{ route('index') }}">หน้าแรก</a></li>
                            <li><a href="{{ route('admin.events.index') }}">จัดการอีเวนต์</a></li>
                            <li><a href="#">จัดการล็อก</a></li>
                            <li><a href="#">จัดการผู้ใช้</a></li>
                            <li><a href="{{ route('admin.booking.manage') }}">คำขออนุมัติ</a></li>
                            <li><a href="{{ route('admin.reports.bookings') }}">รายงานการจอง</a></li>
                            <li class="drop">บัญชีผู้ใช้
                                <ul class="down">
                                    <li><a href="#">แก้ไขบัญชีผู้ใช้</a></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" style="background:none;border:none;cursor:pointer;">
                                                ออกจากระบบ
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endif
                    @endauth
                </ul>
            </nav>
        </div>
    </header>
    <div class="report-header">
        <h1>รายงานการจอง</h1>

        {{-- ฟอร์มเลือกเดือน --}}
        <form method="GET" action="{{ route('admin.reports.bookings') }}">
            <label>เดือน :</label>
            <input type="month" name="month" value="{{ $selectedMonth ?? '' }}">
            <button type="submit">ค้นหา</button>
        </form>
    </div>


    @if(!empty($selectedMonth))
        <div class="report-card">
            <h2>รายงานการจองของเดือน
                {{ \Carbon\Carbon::createFromDate($year, $month, 1)->locale('th')->isoFormat('MMMM YYYY') }}
            </h2>

            <table class="report-table">
                <tr>
                    <th>ล็อก</th>
                    <th>ชื่อ - นามสกุล</th>
                    <th>ชื่อร้าน</th>
                    <th>รายละเอียดร้าน</th>
                </tr>
                @forelse($bookings as $b)
                    <tr>
                        <td>{{ $b->stall_code }}</td>
                        <td>{{ $b->fullname }}</td>
                        <td>{{ $b->shop_name }}</td>
                        <td>{{ $b->shop_detail }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">ไม่มีการจองในเดือนนี้</td>
                    </tr>
                @endforelse
            </table>
        </div>

        <div class="report-summary">
            <h3>สรุปผลการจอง</h3>
            <p>เดือน / ปี :
                <span>{{ \Carbon\Carbon::createFromDate($year, $month, 1)->locale('th')->isoFormat('MMMM YYYY') }}</span>
            </p>
            <p>จองแล้ว : <span>{{ $totalBooked }}</span></p>
            <p>ว่าง : <span>{{ $totalAvailable }}</span></p>
        </div>


    @endif
</body>

</html>