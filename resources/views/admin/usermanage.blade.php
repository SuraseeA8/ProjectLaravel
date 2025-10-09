@extends('layouts.app')

@section('title', 'การจัดการผู้ใช้')

@section('content')
<div class="container py-4">
    <h2 class="mb-3 text-center">ข้อมูลผู้ค้าและชื่อร้าน</h2>

    {{-- ค้นหา --}}
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

<style>
/* ===== Global (ไม่แตะธีมเดิม) ===== */
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

    /* ===== กล่องหลักให้กึ่งกลาง และจัดวางเนื้อหาตรงกลาง ===== */
    .container.py-4{
    max-width:1200px;
    width:100%;
    margin:20px auto;           /* กึ่งกลางหน้าจอ */
    padding:24px;
    background:#fff;
    border:2px solid var(--brand);
    border-radius:16px;
    box-shadow:0 8px 24px rgba(0,0,0,.08);
    display:flex;               /* จัดเลย์เอาต์ภายในเป็นคอลัมน์แล้วกึ่งกลาง */
    flex-direction:column;
    align-items:center;         /* กึ่งกลางแนวนอนของลูก (เช่น ฟอร์ม/ตาราง) */
    gap:16px;
    }

    /* ===== หัวข้อ ===== */
    h2.mb-3.text-center{
    color:var(--brand);
    font-weight:700;
    text-align:center;          /* เผื่อบางหน้ามีคลาสไม่ครบ */
    width:100%;
    }

    /* ===== ฟอร์มค้นหา “อยู่ตรงกลาง” เสมอ ไม่ว่า wrapper จะเป็นอะไร ===== */
    .container .mb-3,
    .container .mb-3.row,
    .container .mb-3 .col,
    .search-wrap{
    width:100%;
    display:flex;
    justify-content:center;     /* กึ่งกลางแนวนอน */
    }
    .container .mb-3 form,
    .search-wrap form,
    .container form.mb-3{
    width:100%;
    max-width:480px;            /* ความกว้างฟอร์ม */
    margin:0 auto;              /* กึ่งกลาง */
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

    /* ===== ตาราง: อยู่กลาง + กว้างเต็มกล่อง + ธีมสวย ===== */
    .table{
    width:100%;
    max-width:1100px;           /* จำกัดความกว้างสูงสุดเพื่อความอ่านง่าย */
    margin:0 auto;              /* กึ่งกลางภายใน .container */
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

    /* ===== ปุ่มลบ (เหมือนเดิม) ===== */
    .btn-danger{
    background:#f44336;
    color: white;
    border:none;
    border-radius:10px;
    padding:6px 12px;
    font-weight:600;
    }
    .btn-danger:hover{ background:#d32f2f; }

    /* ===== เผื่อมี footer ให้กึ่งกลางเต็มแนวกว้าง ===== */
    .site-footer{
    width:100%;
    background:var(--brand);
    color:#fff;
    text-align:center;
    padding:14px;
    }





</style>