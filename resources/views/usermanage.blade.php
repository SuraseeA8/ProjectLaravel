<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>การจัดการผู้ใช้</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat">
    <style>
        body { font-family: "Montserrat", sans-serif; }
    </style>
</head>
<body class="p-4">
    @extends('layouts.app')

@section('title', 'การจัดการผู้ใช้')

@section('content')
<div class="container py-4">
    <h2 class="mb-3">ข้อมูลผู้ค้าและชื่อร้าน</h2>

    {{-- ค้นหา --}}
    <form method="GET" action="" class="mb-3" style="max-width: 420px;">
        <input type="text" name="keyword" class="form-control"
            placeholder="ค้นหาชื่อ/นามสกุล/อีเมล..."
            value="{{ request('keyword') }}">
    </form>

    <table class="table table-striped">
        <thead class="table-success">
            <tr>
                <th>รหัสผู้ใช้</th>
                <th>ชื่อ</th>
                <th>นามสกุล</th>
                <th>อีเมล</th>
                <th>ชื่อร้าน</th>
                <th class="text-center">การจัดการ</th>
            </tr>
        </thead>
        <tbody>
        @php
            $kw = trim(mb_strtolower(request('keyword', ''), 'UTF-8'));
        @endphp

        @forelse($users as $user)
            @continue($user->role_id !== 2) {{-- แสดงเฉพาะผู้ค้า --}}

            @php
                // ตัวกรองแบบง่าย ๆ ในมุมมอง (ถ้าอยากให้ไว ให้ย้ายไป where ใน Controller)
                $haystack = mb_strtolower(($user->users_fname ?? '')
                        .($user->users_lname ?? '')
                        .($user->email ?? ''), 'UTF-8');
                $visible = ($kw === '') || str_contains($haystack, $kw);

                // หา shop_name จากคอลเลกชัน $shops
                $shop = $shops->firstWhere('user_id', $user->id);
                $shopName = $shop->shop_name ?? '-';
            @endphp

            @continue(!$visible)

            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->users_fname }}</td>
                <td>{{ $user->users_lname }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $shopName }}</td>
                <td class="text-center">
                    <form action="{{ route('admin.users.delete', $user->id) }}"
                        method="POST"
                        onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบผู้ใช้นี้?');"
                        class="d-inline">
                        @csrf
                        <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" 
                            onsubmit="return confirm('คุณแน่ใจว่าจะลบผู้ใช้นี้?');">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm">ลบ</button>
                        </form>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center">ไม่มีข้อมูลผู้ใช้</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection

</body>
</html>