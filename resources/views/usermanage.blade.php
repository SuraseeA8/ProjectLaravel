@extends('layouts.app')

@section('title', 'การจัดการผู้ใช้')

@section('content')
<div class="container py-4">
    <h2 class="mb-3 text-center">ข้อมูลผู้ค้าและชื่อร้าน</h2>

    <form method="GET" action="" class="mb-3 " style="max-width: 420px;" >
        <input type="text" name="keyword" class="form-control "
            placeholder="ค้นหาชื่อ..."
            value="{{ request('keyword') }}">
    </form>

    <table class="table table-striped">
        <thead class="table-warning">
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
            
                $shop = $shops->firstWhere('user_id', $user->id);
                $shopName = $shop->shop_name ?? '-';

                $haystack = mb_strtolower(($user->users_fname ?? '')
                        .($shop->shop_name ?? '-')
                        .($user->users_lname ?? '')
                        .($user->email ?? ''), 'UTF-8');
                $visible = ($kw === '') || str_contains($haystack, $kw);

                
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

<style>
    :root{
    --brand:#E68F36;
    --brand-2:#d87c2e;
    --bg-all:#FFFFCC;
    --line:#f0d9c3;
    --text:#333;
    }
    html,body{height:100%}
    body{
    background:var(--bg-all) !important;
    font-family:'Kanit',sans-serif;
    }

    .container.py-4{
    max-width:1200px;
    width:100%;
    margin:20px auto;           
    padding:24px;
    background:#fff;
    border:2px solid var(--brand);
    border-radius:16px;
    box-shadow:0 8px 24px rgba(0,0,0,.08);
    display:flex;              
    flex-direction:column;
    align-items:center;      
    gap:16px;
    }

    h2.mb-3.text-center{
    color:var(--brand);
    font-weight:700;
    text-align:center;        
    width:100%;
    }

  
    .container .mb-3,
    .container .mb-3.row,
    .container .mb-3 .col,
    .search-wrap{
    width:100%;
    display:flex;
    justify-content:center;     
    }
    .container .mb-3 form,
    .search-wrap form,
    .container form.mb-3{
    width:100%;
    max-width:480px;           
    margin:0 auto;      
    }
    .container .mb-3 .form-control,
    .container form.mb-3 .form-control{
    border-radius:10px;
    border:1.5px solid #ddd;
    padding:.6rem .9rem;
    }
    .container .mb-3 .form-control:focus,
    .container form.mb-3 .form-control:focus{
    border-color:var(--brand);
    box-shadow:0 0 0 .2rem rgba(230,143,54,.15);
    }

    .table{
    width:100%;
    max-width:1100px;          
    margin:0 auto;            
    background:#fff;
    overflow:hidden;
    box-shadow:0 6px 16px rgba(0,0,0,.08);
    }
    .table thead.table-warning{
    background:linear-gradient(90deg, var(--brand), var(--brand-2)) !important;
    color:#fff;
    }
    .table thead th{
    text-align:center;
    font-weight:600;
    padding:14px 12px;
    border-bottom:none !important;
    }
    .table tbody td{
    text-align:center;
    color:var(--text);
    vertical-align:middle;
    border-color:var(--line) !important;
    padding:12px 10px;
    }
    .table.table-striped>tbody>tr:nth-of-type(even)>*{
    background:#FFF9F3 !important;
    }

    .btn-danger{
    background:#f44336;
    color: white;
    border:none;
    border-radius:10px;
    padding:6px 12px;
    font-weight:600;
    }
    .btn-danger:hover{ background:#d32f2f; }

    .site-footer{
    width:100%;
    background:var(--brand);
    color:#fff;
    text-align:center;
    padding:14px;
    }

</style>