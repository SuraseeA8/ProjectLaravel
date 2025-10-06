<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/navbar_vendor.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Kanit&display=swap" rel="stylesheet">
    {{-- เดิมเขียนผิดเป็น rel="stylesheet" ไปที่ไฟล์ js --}}
    <script src="{{ asset('js/stalls.js') }}" defer></script>
    <title>@yield('title', 'ระบบจองตลาดนัด')</title>
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
                        <li><a class="btn" href="{{ route('register') }}">สมัครสมาชิก</a></li>
                    @endguest

                    {{-- Auth --}}
                    @auth
                        @if(Auth::user()->role_id == 2)
                            <li class="home"><a href="{{ route('vendor.home') }}">หน้าแรก</a></li>
                            <li><a href="{{ route('vendor.events') }}">ประกาศ</a></li>
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
                            <li class="home"><a href="{{ route('index') }}">หน้าแรก</a></li>
                            <li><a href="{{ route('admin.events.index') }}">จัดการอีเวนต์</a></li>
                            <li><a href="#">จัดการล็อก</a></li>
                            <li><a href="{{ route('admin.users.index') }}">จัดการผู้ใช้</a></li>
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
    <main>
        @yield('content')
    </main>

    <footer>
        <p>&copy; 2025 ระบบจองล็อกตลาดนัด</p>
    </footer>
</body>
</html>
