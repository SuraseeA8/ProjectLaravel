@extends('layouts.app')

@section('title', 'บัญชีผู้ใช้')

@section('content')
<div class="container py-4" style="max-width: 700px;">
    <div class="card p-4" style="border:2px solid orange;border-radius:20px;background-color:#fffbe9;">
        <h4 class="text-center mb-4 fw-bold">บัญชีผู้ใช้</h4>

        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
        @if(session('error'))   <div class="alert alert-danger">{{ session('error') }}</div>   @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.profile.update') }}" method="POST" autocomplete="off">
            @csrf
            @method('PUT')

            <input type="text" name="fake_user"  autocomplete="username" style="display:none">
            <input type="password" name="fake_pass" autocomplete="new-password" style="display:none">

            <div class="mb-3">
                <label class="form-label fw-bold">ชื่อ</label>
                <input type="text" name="Users_Fname" class="form-control rounded-pill border-warning"
                    value="{{ old('Users_Fname', $user->users_fname ?? '') }}">
                @error('Users_Fname') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">นามสกุล</label>
                <input type="text" name="Users_Lname" class="form-control rounded-pill border-warning"
                    value="{{ old('Users_Lname', $user->users_lname ?? '') }}">
                @error('Users_Lname') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">อีเมล</label>
                <input type="email" name="Email" class="form-control rounded-pill border-warning"
                    value="{{ old('Email', $user->email ?? '') }}" autocomplete="email">
                @error('Email') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">เบอร์โทร</label>
                <input type="tel" name="phone" class="form-control rounded-pill border-warning"
                    value="{{ old('phone', $user->phone ?? '') }}"
                    inputmode="numeric" pattern="[0-9]{9,10}" maxlength="10"
                    placeholder="เช่น 0812345678" autocomplete="tel">
                @error('phone') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3 text-center">
                <button type="button" class="btn btn-outline-dark rounded-pill" onclick="togglePasswordSection()">
                    เปลี่ยนรหัสผ่าน
                </button>
            </div>

            <div id="passwordSection" style="display:none;">


                <div class="mb-3">
                    <label class="form-label fw-bold">รหัสผ่านเดิม</label>
                    <input type="password" id="current_password" name="current_password" class="form-control"
                        autocomplete="current-password" readonly onfocus="this.removeAttribute('readonly');" value="">
                    @error('current_password') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">รหัสผ่านใหม่</label>
                    <input type="password" id="password" name="password" class="form-control"
                        autocomplete="new-password" readonly onfocus="this.removeAttribute('readonly');" value="">
                    @error('password') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">ยืนยันรหัสผ่านใหม่</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                        autocomplete="new-password" readonly onfocus="this.removeAttribute('readonly');" value="">
                </div>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-warning rounded-pill px-4">
                    บันทึกการเปลี่ยนแปลง
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function togglePasswordSection() {
    const section = document.getElementById('passwordSection');
    const willShow = (section.style.display === 'none' || section.style.display === '');
    section.style.display = willShow ? 'block' : 'none';

    ['current_password','password','password_confirmation'].forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        el.required = willShow;
        if (!willShow) { el.value = ''; el.required = false; el.setAttribute('readonly','readonly'); }
    });
}
</script>

<style>
    :root{
    --brand:#E68F36;
    --brand2:#f2a65a;
    --bg:#fffde7;
    --card:#ffffff;
    --text:#333;
    --muted:#727272;
    --line:#e9e9e9;
    --shadow:0 8px 24px rgba(0,0,0,.08);
    }

    body{
    background:var(--bg);
    font-family:'Kanit', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans Thai", sans-serif;
    color:var(--text);
    }

    .container.py-4{
    
    margin-left:auto !important;
    margin-right:auto !important;
    margin-top: 30px !important;
    margin-bottom: 30px !important;
    max-width:700px !important;  
    }
    .container.py-4 > .card{
    background:var(--card) !important;
    border:1.5px solid var(--brand) !important;
    border-radius:16px !important;
    box-shadow:var(--shadow) !important;
    padding:24px !important;
    margin-left:auto !important;     
    margin-right:auto !important;
    }

    .container.py-4 h4{
    font-size:22px;
    font-weight:700;
    color:var(--brand);
    margin-bottom:14px;
    }

    

    .container.py-4 form .mb-3{
    display:grid;
    grid-template-columns: 260px 1fr;  
    align-items:center;
    column-gap:18px;
    row-gap:8px;
    margin-bottom:14px;
    }
    .form-label{
    font-weight:700;
    color:#222;
    margin:0;
    text-align:right;
    padding-right:4px;
    }

    .form-control{
    border:1.5px solid #ddd !important;
    border-radius:10px !important;
    padding:10px 12px !important;
    font-size:14px !important;
    background:#fff !important;
    transition:border-color .2s, box-shadow .2s, background .2s;
    }
    .form-control:focus{
    outline:none;
    border-color:var(--brand) !important;
    box-shadow:0 0 0 4px rgba(230,143,54,.15) !important;
    }

    input:-webkit-autofill{
    -webkit-box-shadow: 0 0 0px 1000px #fff inset !important;
    -webkit-text-fill-color: var(--text) !important;
    }

    .btn{
    border-radius:999px !important;
    padding:10px 18px !important;
    font-size:14px !important;
    font-weight:700 !important;
    border:1px solid transparent;
    transition:transform .15s ease, box-shadow .2s, background .2s, color .2s, border-color .2s;
    }
    .btn:hover{ transform:translateY(-1px); }

    .btn-warning{
    background:var(--brand) !important;
    color:#fff !important;
    border-color:transparent !important;
    box-shadow:0 6px 16px rgba(230,143,54,.28);
    }
    .btn-warning:hover{ background:#d87c2e !important; }

    .container.py-4 .mb-3.text-center .btn{
    min-width:220px;
    background:var(--brand) !important;
    color:#fff !important;
    border-color:transparent !important;
    box-shadow:0 6px 16px rgba(230,143,54,.28);
    }
    .container.py-4 .mb-3.text-center .btn:hover{
    background:#d87c2e !important;
    }

    #passwordSection{
    background:#fffdfa;
    border:1px solid #ffe6cc;
    border-radius:12px;
    padding:16px;
    margin-top:10px;
    }
    #passwordSection .alert{
    grid-column: 1 / -1;
    margin-bottom:8px;
    border-radius:10px;
    }
    #passwordSection .mb-3{
    display:grid;
    grid-template-columns: 260px 1fr;
    align-items:center;
    column-gap:18px;
    row-gap:8px;
    }

    .container.py-4 .text-center.mt-4{
    border-top:1px dashed var(--line);
    padding-top:12px;
    }

    .alert-success{
    background:#e8f5e9 !important;
    border:1px solid #4CAF50 !important;
    color:#2e7d32 !important;
    border-radius:10px;
    }
    .alert-danger{
    background:#ffebee !important;
    border:1px solid #f44336 !important;
    color:#b71c1c !important;
    border-radius:10px;
    }

    :root{
    --brand:#E68F36;
    --line:#e9e9e9;
    }

   
    .card .row-span.toolbar{
    display:flex;
    justify-content:center;   
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

@endsection
