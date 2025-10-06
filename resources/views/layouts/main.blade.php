<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Market Booking')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Prompt', sans-serif;
            background-color: #fff8e1;
        }

        .navbar {
            background-color: #f7931e;
        }

        .navbar-brand {
            font-weight: 700;
            color: white !important;
        }

        .navbar-nav .nav-link {
            color: white !important;
            margin-right: 15px;
            font-weight: 500;
        }

        .navbar-nav .nav-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg" style="background-color: #f28c28; color: white; font-family: 'Prompt';">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold text-white" href="#">Market Booking</a>

    <ul class="navbar-nav ms-auto">
        <li class="nav-item">
            <a href="{{ route('admin.stalls.index') }}" class="nav-link text-white">หน้าแรก</a>
        </li>

        <li class="nav-item">
            <a href="{{ route('admin.stalls.index') }}" class="nav-link text-white">จัดการล็อก</a>
        </li>
{{-- {{ route('admin.users.index') }} --}}
        <li class="nav-item">
            <a href="#" class="nav-link text-white">จัดการผู้ใช้</a>
        </li>
{{-- {{ route('admin.users.index') }} --}}
        <li class="nav-item">
            <a href="#" class="nav-link text-white">จัดการอีเวนต์</a>
        </li>
        {{-- {{ route('admin.booking.manage') }} --}}

        <li class="nav-item">
            <a href="#" class="nav-link text-white">คำขออนุมัติ</a>
        </li>
{{-- {{ route('admin.booking.manage') }} --}}
        <li class="nav-item">
            <a href="#" class="nav-link text-white">รายงานการจอง</a>
        </li>
    </ul>



        <!-- เมนู dropdown -->
        <li class="nav-item dropdown" style="position: relative;">
            <a href="#" class="nav-link text-white dropdown-toggle" id="userMenu" role="button" data-bs-toggle="dropdown">
            บัญชีผู้ใช้
            </a>
            <ul class="dropdown-menu dropdown-menu-end mt-2" style="border-color: #f28c28;">
            <li>
                {{-- {{ route('admin.profile') }} --}}
                <a class="dropdown-item text-center" href="#" style="color: #f28c28;">แก้ไขบัญชีผู้ใช้</a>
            </li>
            <li>
                <a class="dropdown-item text-center" href="#" style="color: #f28c28;">ออกจากระบบ</a>
            </li>
            </ul>
        </li>
        </ul>
    </div>
    </nav>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


    {{-- 🔹 ส่วนเนื้อหาเฉพาะแต่ละหน้า --}}
    <div class="container py-4">
        @yield('content')
    </div>

</body>
</html>