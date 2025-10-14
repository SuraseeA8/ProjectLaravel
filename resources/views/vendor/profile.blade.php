    @extends('layouts.app')

    @section('title', 'บัญชีผู้ใช้ - Vendor')

    @section('content')

        <div class="card">
            <h2>บัญชีผู้ใช้</h2>

            <form action="{{ route('vendor.profile.update') }}" method="POST" class="grid">
            @csrf

                <label for="users_fname">ชื่อ</label>
                <input id="users_fname" name="users_fname" type="text" value="{{ old('users_fname', $user->users_fname) }}">

                <label for="users_lname">นามสกุล</label>
                <input id="users_lname" name="users_lname" type="text" value="{{ old('users_lname', $user->users_lname) }}">

                <label for="email">อีเมล</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}">

                <label for="phone">เบอร์โทร</label>
                <input id="phone" name="phone" type="text" value="{{ old('phone', $user->phone) }}">

                <div class="row-span toolbar">
                    <button type="button" class="btn btn-ghost" id="togglePasswordBtn">เปลี่ยนรหัสผ่าน</button>
                    <button type="submit" class="btn btn-brand">บันทึกการเปลี่ยนแปลง</button>
                </div>
            </form>

            <div id="passwordPanel" style="display: {{ session('show_password_panel') ? 'block' : 'none' }}; margin-top:12px;">
            <form action="{{ route('vendor.profile.password') }}" method="POST" class="grid">
                @csrf
                <label for="current_password">รหัสผ่านเดิม</label>
                <input id="current_password" name="current_password" type="password" autocomplete="current-password">

                <label for="new_password">รหัสผ่านใหม่</label>
                <input id="new_password" name="new_password" type="password" autocomplete="new-password">

                <label for="new_password_confirmation">ยืนยันรหัสผ่านใหม่</label>
                <input id="new_password_confirmation" name="new_password_confirmation" type="password" autocomplete="new-password">

                <div class="row-span toolbar">
                <button type="submit" class="btn btn-brand">บันทึกการเปลี่ยนแปลง</button>
                </div>
            </form>
            </div>
        </div>

        <div class="card">
            <h2>ข้อมูลร้านค้า</h2>
            <form action="{{ route('vendor.shop.update') }}" method="POST" class="grid">
            @csrf

            <label for="shop_name">ชื่อร้าน</label>
            <input id="shop_name" name="shop_name" type="text" value="{{ old('shop_name', $shop->shop_name ?? '') }}">

            <label for="description">รายละเอียด</label>
            <textarea id="description" name="description">{{ old('description', $shop->description ?? '') }}</textarea>

            <div class="row-span toolbar">
                <button type="submit" class="btn btn-brand">บันทึกข้อมูลร้าน</button>
            </div>

            </form>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const btn   = document.getElementById('togglePasswordBtn');
                const panel = document.getElementById('passwordPanel');
                if (!btn || !panel) return;

                btn.addEventListener('click', () => {
                const isHidden = panel.style.display === 'none' || getComputedStyle(panel).display === 'none';
                panel.style.display = isHidden ? 'block' : 'none';
                if (isHidden) {
                    const firstPwd = panel.querySelector('input[type="password"]');
                    firstPwd && firstPwd.focus();
                }
                });

                const shouldOpen = {!! json_encode(
                $errors->hasAny(['current_password','new_password','new_password_confirmation']) || session('show_password_panel')
                ) !!};
                if (shouldOpen) panel.style.display = 'block';
            });
        </script>


        @endsection

        

    <style>
        :root{
            --brand:#E68F36;
            --brand-2:#f2a65a;
            --bg:#fffde7;
            --card:#ffffff;
            --text:#333;
            --muted:#727272;
            --line:#e9e9e9;
            --shadow:0 8px 24px rgba(0,0,0,.08);
        }

        body{ background:var(--bg); font-family:'Kanit',sans-serif; color:var(--text); }

        .card{
            background:var(--card);
            border:1.5px solid var(--brand);
            border-radius:16px;
            padding:22px;
            margin:20px auto;
            box-shadow:var(--shadow);
            max-width:980px;
        }

        .card h2{
            font-size:22px;
            font-weight:700;
            color:var(--brand);
            margin-bottom:14px;
            text-align:left; 
        }

        .card form.grid{
            display:grid;
            grid-template-columns: 1fr 1fr;
            gap:14px 18px;
        }

        .card label{
            font-weight:600;
            color:#222;
            margin-top:6px;
        }

        .card input[type="text"],
        .card input[type="email"],
        .card input[type="password"],
        .card textarea{
            width:100%;
            padding:10px 12px;
            border:1.5px solid #ddd;
            border-radius:10px;
            font-size:14px;
            background:#fff;
            transition:border-color .2s, box-shadow .2s, background .2s;
        }

        .card input[disabled]{
            background:#f7f7f7;
            color:#888;
            cursor:not-allowed;
        }

        .card textarea{
            min-height:110px;
            resize:vertical;
            line-height:1.45;
        }

        .card input:focus,
        .card textarea:focus{
            outline:none;
            border-color:var(--brand);
            box-shadow:0 0 0 4px rgba(230,143,54,.15);
        }

        .card .row-span.toolbar{
            grid-column:1 / -1;            
            display:flex;
            justify-content:space-between;    
            align-items:center;
            gap:12px;
            margin-top:6px;
            padding-top:10px;
            border-top:1px dashed var(--line);
        }

        /* ปุ่ม */
        .btn{
            display:inline-block;
            padding:10px 18px;
            border-radius:999px;
            font-size:14px;
            font-weight:600;
            text-decoration:none;
            cursor:pointer;
            border:1px solid transparent;
            transition:transform .15s ease, box-shadow .2s, background .2s, color .2s, border-color .2s;
            user-select:none;
            white-space:nowrap;
        }
        .btn:hover{ transform:translateY(-1px); }

        .btn-brand{
            background:var(--brand);
            color:#fff;
            box-shadow:0 6px 16px rgba(230,143,54,.28);
        }
        .btn-brand:hover{ background:#d87c2e; }

        .btn-ghost{
            background:#fff;
            color:#333;
            border-color:#d9d9d9;
        }
        .btn-ghost:hover{
            background:#fafafa;
            border-color:#cfcfcf;
        }

        .muted{ color:var(--muted); font-size:13px; }

        #passwordPanel{
            background:#fffdfa;
            border:1px solid #ffe6cc;
            border-radius:12px;
            padding:16px;
            margin-top:10px;
        }
        #passwordPanel form.grid{
            grid-template-columns: 1fr 1fr;
        }

        .card form.grid > label{ align-self:end; }
        .card form.grid > input,
        .card form.grid > textarea{ align-self:start; }

        .card form.grid > label + input,
        .card form.grid > label + textarea{ margin-top:-6px; }

        :root{
        --brand:#E68F36;
        --line:#e9e9e9;
        }

        .card .row-span.toolbar{
        display:flex;
        justify-content:center;    /
        align-items:center;
        gap:12px;                 
        margin-top:6px;
        padding-top:10px;
        border-top:1px dashed var(--line);
        }

        .container.py-4 .mb-3.text-center,
        .container.py-4 .text-center.mt-4{
        display:flex;
        justify-content:center;
        align-items:center;
        gap:12px;
        }

        #togglePasswordBtn,                               
        .card .btn-brand,                               
        .container.py-4 .mb-3.text-center .btn,         
        .container.py-4 .text-center.mt-4 .btn,        
        .btn-warning, .btn-outline-dark, .btn-ghost {     
        background: var(--brand) !important;
        color: #fff !important;
        border: 1px solid transparent !important;
        border-radius: 999px !important;
        padding: 10px 18px !important;
        font-weight: 700 !important;
        box-shadow: 0 6px 16px rgba(230,143,54,.28) !important;
        transition: transform .15s ease, box-shadow .2s !important;
        min-width: 220px;           
        }
        #togglePasswordBtn:hover,
        .card .btn-brand:hover,
        .container.py-4 .mb-3.text-center .btn:hover,
        .container.py-4 .text-center.mt-4 .btn:hover,
        .btn-warning:hover, .btn-outline-dark:hover, .btn-ghost:hover{
        transform: translateY(-1px);
        background: #d87c2e !important;
        }

        .container.py-4{
        margin-left:auto !important;
        margin-right:auto !important;
        }


    </style>

