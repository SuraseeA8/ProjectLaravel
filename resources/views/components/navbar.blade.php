<nav class="navbar">
    <div class="logo">ตลาดนัดออนไลน์</div>
    <ul class="menu">
        <li><a href="#">หน้าแรก</a></li>
        <li><a href="#">ข่าวสาร</a></li>

        @guest
            {{-- ยังไม่ login --}}
            <li><a href="/login">เข้าสู่ระบบ</a></li>
            <li><a class="btn" href="/register">สมัครสมาชิก</a></li>
        @else
            {{-- login แล้ว --}}
            @if(Auth::user()->role === 'vendor')
                <li><a href="#">จองล็อก</a></li>
                <li><a href="#">การจองของฉัน</a></li>
                <li><a href="#">โปรไฟล์</a></li>
            @elseif(Auth::user()->role === 'admin')
                <li><a href="#">จัดการล็อก</a></li>
                <li><a href="#">อนุมัติการจอง</a></li>
                <li><a href="#">รายงานสรุป</a></li>
            @endif

            <li>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="logout-btn">ออกจากระบบ</button>
                </form>
            </li>
        @endguest
    </ul>
</nav>
