<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/navbar_vendor.css') }}">
    <link rel="stylesheet" href="{{ asset('css/stalls.css') }}">
    <script src="{{ asset('js/stalls.js') }}"></script>
    <title>@yield('title', 'ระบบจองตลาดนัด')</title>
</head>
<body>
    <header>
        <nav class="navbar">
            @auth
                @if(auth()->check() && auth()->user()->role_id == 2)
                    {{-- Vendor Navbar --}}
                    <div class="logo">Market Booking</div>
                        <ul class="menu">
                            <li><a href="{{ route('vendor.home') }}">หน้าแรก</a></li>
                            <li><a href="{{ route('vendor.events') }}">ประกาศ</a></li>
                            <li><a href="{{ route('vendor.stalls') }}">จองล็อก</a></li>
                            <li><a href="{{ route('vendor.booking.status') }}">รายการจอง</a></li>
                            <li><a href="#">ติดต่อเรา</a></li>
                            <li><a href="{{ route('vendor.profile') }}">บัญชีผู้ใช้</a></li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn">ออกจากระบบ</button>
                            </form>
                        </ul>
                    </div>
                @elseif(auth()->check() && auth()->user()->role_id == 1)
                    {{-- Admin Navbar --}}
                    <div class="logo">Market Booking</div>
                    <ul class="menu">
                        <li><a href="#">จัดการล็อก</a></li>
                        <li><a href="#">อนุมัติการจอง</a></li>
                        <li><a href="#">รายงานสรุป</a></li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn">ออกจากระบบ</button>
                        </form>
                    </ul>
                @endif
            @endauth
        </nav>
    </header>

    <main class="container">
        @yield('content')
    </main>

    
</body>
</html>