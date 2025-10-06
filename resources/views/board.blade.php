<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>จัดการกิจกรรม - Admin</title>
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

        .event a {
            color: #E68F36;
        }

        nav ul li a {
            color: black;
            text-decoration: none;
        }

        main {
            flex: 1;
            padding: 20px;
        }

        footer {
            background: #E68F36;
            color: white;
            text-align: center;
            padding: 15px;
        }

        h2 {
            margin-bottom: 20px;
        }

        .btn {
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
        }

        .btn-add {
            background: #4CAF50;
            color: white;
        }

        .btn-del {
            background: #f44336;
            color: white;
            border: none;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }

        th {
            background: #E68F36;
            color: white;
        }

        img {
            max-width: 100px;
            border-radius: 4px;
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
            /* ให้ลอยแยกจาก flex หลัก */
            top: 100%;
            /* อยู่ใต้ปุ่มหลัก */
            left: 0;
            /* ชิดซ้าย */
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

        .event-grid {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 40px;
            max-width: 900px;
            margin: 30px auto;
            padding: 20px;
        }

        .event-card {
            width: 100%;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .event-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.25);
        }

        .event-image img {
            width: 100%;
            height: 300px;
            /* ความสูงรูป */
            object-fit: cover;
            /* ให้รูปเต็มพื้นที่ โดยไม่บีบ */
            display: block;
        }

        .event-body {
            padding: 20px;
            text-align: center;
        }

        .event-body h3 {
            font-size: 28px;
            margin-bottom: 12px;
            color: #E68F36;
        }

        .event-body .event-detail {
            font-size: 16px;
            color: #444;
            margin-bottom: 15px;
            line-height: 1.6;
        }
    </style>
</head>

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
                        <li><a href="{{ route('board') }}">ประกาศ</a></li>
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
                            <li><a href="#">คำขออนุมัติ</a></li>
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

    <div class="event-grid">
        @forelse($events as $event)
            <div class="event-card">
                @if($event->img_path)
                    <div class="event-image">
                        <img src="{{ asset('storage/' . $event->img_path) }}" alt="{{ $event->title }}">
                    </div>
                @else
                    <div class="event-image">
                        <img src="https://via.placeholder.com/800x400?text=No+Image" alt="no image">
                    </div>
                @endif

                <div class="event-body">
                    <h3>{{ $event->title }}</h3>
                    <p class="event-detail">{{ $event->detail }}</p>
                    <p>เริ่ม: {{ \Carbon\Carbon::parse($event->start_date)->format('d/m/Y') }}</p>
                    <p>สิ้นสุด: {{ \Carbon\Carbon::parse($event->end_date)->format('d/m/Y') }}</p>
                </div>
            </div>
        @empty
            <p style="text-align:center;">ยังไม่มีกิจกรรม</p>
        @endforelse
    </div>


</body>

</html>