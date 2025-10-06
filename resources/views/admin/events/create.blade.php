<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>เพิ่มกิจกรรมใหม่ - Admin</title>
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

        form {
            max-width: 500px;
            margin: auto;
        }

        label {
            display: block;
            margin-top: 12px;
        }

        input,
        textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button {
            margin-top: 15px;
            padding: 8px 15px;
            border: none;
            border-radius: 6px;
            background: #4CAF50;
            color: white;
            cursor: pointer;
        }

        button:hover {
            background: #45a049;
        }

        a {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            color: #E68F36;
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
                            <li class="event"><a href="{{ route('admin.events.create') }}">ประกาศ</a></li>
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
    <h2>เพิ่มกิจกรรมใหม่</h2>

    <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label>ชื่อกิจกรรม</label>
        <input type="text" name="title" required>

        <label>รายละเอียด</label>
        <textarea name="detail" rows="4" required></textarea>

        <label>วันที่เริ่ม</label>
        <input type="date" name="start_date" required>

        <label>วันที่สิ้นสุด</label>
        <input type="date" name="end_date" required>

        <label>อัปโหลดรูปภาพ</label>
        <input type="file" name="img_path" accept="image/*">

        <button type="submit">บันทึก</button>
    </form>

    <a href="{{ route('admin.events.index') }}">⬅ กลับ</a>
</body>

</html>