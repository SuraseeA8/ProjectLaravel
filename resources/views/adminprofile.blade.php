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

        {{-- ฟอร์มเดียว: อัปเดตโปรไฟล์ + (ถ้ากรอกรหัส) เปลี่ยนรหัสผ่าน --}}
        <form action="{{ route('admin.profile.update') }}" method="POST" autocomplete="off">
            @csrf
            @method('PUT')

            {{-- ป้องกัน auto-fill ของเบราว์เซอร์ --}}
            <input type="text" name="fake_user"  autocomplete="username" style="display:none">
            <input type="password" name="fake_pass" autocomplete="new-password" style="display:none">

            {{-- ชื่อ --}}
            <div class="mb-3">
                <label class="form-label fw-bold">ชื่อ</label>
                <input type="text" name="Users_Fname" class="form-control rounded-pill border-warning"
                       value="{{ old('Users_Fname', $user->users_fname ?? '') }}">
                @error('Users_Fname') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            {{-- นามสกุล --}}
            <div class="mb-3">
                <label class="form-label fw-bold">นามสกุล</label>
                <input type="text" name="Users_Lname" class="form-control rounded-pill border-warning"
                       value="{{ old('Users_Lname', $user->users_lname ?? '') }}">
                @error('Users_Lname') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            {{-- อีเมล --}}
            <div class="mb-3">
                <label class="form-label fw-bold">อีเมล</label>
                <input type="email" name="Email" class="form-control rounded-pill border-warning"
                       value="{{ old('Email', $user->email ?? '') }}" autocomplete="email">
                @error('Email') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            {{-- เบอร์โทร (ใช้เฉพาะคอลัมน์ phone) --}}
            <div class="mb-3">
                <label class="form-label fw-bold">เบอร์โทร</label>
                <input type="tel" name="phone" class="form-control rounded-pill border-warning"
                       value="{{ old('phone', $user->phone ?? '') }}"
                       inputmode="numeric" pattern="[0-9]{9,10}" maxlength="10"
                       placeholder="เช่น 0812345678" autocomplete="tel">
                @error('phone') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            {{-- ปุ่มโชว์/ซ่อน ส่วนเปลี่ยนรหัสผ่าน --}}
            <div class="mb-3 text-center">
                <button type="button" class="btn btn-outline-dark rounded-pill" onclick="togglePasswordSection()">
                    เปลี่ยนรหัสผ่าน
                </button>
            </div>

            {{-- ส่วนรหัสผ่าน (ซ่อนไว้ก่อน) --}}
            <div id="passwordSection" style="display:none;">
                <div class="alert alert-warning small">
                    หากไม่ต้องการเปลี่ยนรหัสผ่าน ให้ปล่อยช่องด้านล่างว่างไว้ทั้งหมด
                </div>

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
/* ล้างพื้นหลังฟ้า auto-fill ของ Chrome */
input:-webkit-autofill,
input:-webkit-autofill:focus,
input:-webkit-autofill:hover {
    -webkit-box-shadow: 0 0 0px 1000px #ffffff inset !important;
    box-shadow: 0 0 0px 1000px #ffffff inset !important;
    -webkit-text-fill-color: inherit !important;
}
</style>
@endsection
