<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/navbar_vendor.css') }}">
    {{-- เดิมเขียนผิดเป็น rel="stylesheet" ไปที่ไฟล์ js --}}
    <script src="{{ asset('js/stalls.js') }}" defer></script>
    <title>@yield('title', 'ระบบจองตลาดนัด')</title>
</head>
<body>
    <header>
        <nav class="navbar">
        <div class="logo">Market Booking</div>

        {{-- เมนูสำหรับ "ยังไม่ล็อกอิน" --}}
        @guest
        <ul class="menu">
            <li><a href="{{ route('index') }}">หน้าแรก</a></li>
            <li><a href="{{ route('vendor.events') }}">ข่าวสาร</a></li>
            <li><a href="{{ route('login') }}">เข้าสู่ระบบ</a></li>
            <li><a class="btn" href="{{ route('register') }}">สมัครสมาชิก</a></li>
        </ul>
        @endguest

        {{-- เมนูสำหรับ "ล็อกอินแล้ว" --}}
        @auth
            @php $role = auth()->user()->role_id ?? null; @endphp

            @if($role == 2) {{-- Vendor --}}
            <ul class="menu">
                <li><a href="{{ route('vendor.home') }}">หน้าแรก</a></li>
                <li><a href="{{ route('vendor.events') }}">ประกาศ</a></li>
                <li><a href="{{ route('vendor.stalls') }}">จองล็อก</a></li>
                <li><a href="{{ route('vendor.booking.status') }}">รายการจอง</a></li>
                <li><a href="{{ route('vendor.profile') }}">บัญชีผู้ใช้</a></li>
                <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn">ออกจากระบบ</button>
                </form>
                </li>
            </ul>
            @elseif($role == 1) {{-- Admin --}}
            <ul class="menu">
                <li><a href="{{ route('admin.stalls') }}">จัดการล็อก</a></li>
                <li><a href="{{ route('admin.payments') }}">อนุมัติการจอง</a></li>
                <li><a href="{{ route('admin.reports') }}">รายงานสรุป</a></li>
                <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn">ออกจากระบบ</button>
                </form>
                </li>
            </ul>
            @else
            {{-- เผื่อ role อื่น ๆ --}}
            <ul class="menu">
                <li><a href="{{ route('index') }}">หน้าแรก</a></li>
                <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn">ออกจากระบบ</button>
                </form>
                </li>
            </ul>
            @endif
        @endauth
        </nav>
    </header>

    <main class="container">
        @yield('content')
    </main>

    <footer class="footer">
        <p>© {{ date('Y') }} ระบบจองตลาดนัด | พัฒนาโดย ทีมโครงงาน</p>
    </footer>
</body>
</html>
