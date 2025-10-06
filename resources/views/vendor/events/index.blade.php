<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>ประกาศ - ระบบจองล็อกตลาดนัด</title>
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

        /* Board Styles */
        .board-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 15px;
        }

        .board-title {
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
            font-weight: bold;
            color: #333;
        }

        .event-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        .event-card {
            border: 1px solid #ddd;
            border-radius: 12px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            transition: transform 0.2s;
        }

        .event-card:hover {
            transform: translateY(-5px);
        }

        .event-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .event-card-body {
            padding: 15px;
            flex-grow: 1;
        }

        .event-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #222;
        }

        .event-detail {
            font-size: 14px;
            color: #666;
            margin-bottom: 12px;
        }

        .event-date {
            font-size: 13px;
            color: #444;
            margin: 2px 0;
        }

        .event-footer {
            padding: 10px 15px;
            border-top: 1px solid #eee;
            text-align: center;
            background: #f9f9f9;
        }

        .btn-view {
            display: inline-block;
            padding: 8px 15px;
            background: #4CAF50;
            color: #fff;
            font-size: 14px;
            text-decoration: none;
            border-radius: 6px;
            transition: background 0.3s;
        }

        .btn-view:hover {
            background: #45a049;
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

        .event-card {
            width: 100%;
            max-width: 700px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            margin: 20px auto;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .event-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.25);
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
                        <li><a href="{{ route('index') }}">หน้าแรก</a></li>
                        <li class="event"><a href="{{ route('public.board') }}">ประกาศ</a></li>
                        <li><a href="#">ติดต่อเรา</a></li>
                        <li><a href="{{ route('login') }}">เข้าสู่ระบบ</a></li>
                        <li><a href="{{ route('register') }}">ลงทะเบียน</a></li>
                    @endguest

                    {{-- Auth --}}
                    @auth
                        @if(Auth::user()->role_id == 2)
                            <li><a href="{{ route('index') }}">หน้าแรก</a></li>
                            <li class="event"><a href="{{ route('vendor.events') }}">ประกาศ</a></li>
                            <li><a href="{{ route('vendor.stalls') }}">จองล็อก</a></li>
                            <li><a href="{{ route('vendor.booking.status') }}">รายการจอง</a></li>
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
                            <li><a href="{{ route('index') }}">หน้าแรก</a></li>
                            <li class="event"><a href="{{ route('admin.events.index') }}">จัดการอีเวนต์</a></li>
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

    <main>
        <div class="board-container">
            <h2 class="board-title">บอร์ดประกาศกิจกรรม</h2>

            <div class="event-grid">
                @forelse($events as $event)
                    <div class="event-card">
                        <div class="event-image">
                            @if($event->img_path)
                                <img src="{{ asset('storage/' . $event->img_path) }}" alt="{{ $event->title }}">
                            @else
                                <img src="https://via.placeholder.com/400x200?text=No+Image" alt="no image">
                            @endif
                        </div>
                        <div class="event-body">
                            <h3>{{ $event->title }}</h3>
                            <p>{{ $event->detail }}</p>
                            <p>เริ่ม: {{ \Carbon\Carbon::parse($event->start_date)->format('d/m/Y') }}</p>
                            <p>สิ้นสุด: {{ \Carbon\Carbon::parse($event->end_date)->format('d/m/Y') }}</p>
                        </div>
                    </div>

                @empty
                    <p style="text-align:center; width:100%">ยังไม่มีกิจกรรม</p>
                @endforelse
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 ระบบจองล็อกตลาดนัด</p>
    </footer>
</body>

</html>