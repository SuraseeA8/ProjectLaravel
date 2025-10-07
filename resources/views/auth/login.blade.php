{{-- resources/views/auth/login.blade.php (Bootstrap 5 + Inline CSS, single file) --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ระบบจองล็อกตลาดนัด') }}</title>

    {{-- Bootstrap 5 CSS (CDN) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root{
        /* ปรับธีม Bootstrap ให้เป็นส้ม */
        --bs-primary: #ea580c;
        --bs-primary-rgb: 234,88,12;

        /* โทนเสริม */
        --bg: #fff8f1;
        --ink: #5a341a;
        --muted: #8b5e34;
        --line: #ffd7b0;
        --primary-600:#f97316;
        --primary-200:#fdba74;

        --radius: 1rem;
        --shadow: 0 12px 32px rgba(234,88,12,.18);
        }

        html,body{height:100%;}
        body{
        background: var(--bg);
        color: var(--ink);
        font-family: system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,'Kanit',sans-serif;
        }

        /* Layout */
        .auth-grid{
        min-height: 100vh;
        }

        /* Left hero */
        .hero{
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, var(--primary-200), #fff 65%);
        }
        .hero-inner{
        max-width: 520px;
        margin-inline: auto;
        padding: 4rem 2rem;
        text-align: center;
        }
        .logo-badge{
        width: 72px; height: 72px;
        border-radius: 22px;
        background: var(--bs-primary);
        display: grid; place-items: center;
        margin: 0 auto 1rem;
        box-shadow: var(--shadow);
        }
        .hero-title{
        color: #b45309;
        font-weight: 800;
        }
        .hero-sub{ color: color-mix(in oklab, var(--ink) 60%, transparent); }

        /* Right card */
        .auth-card{
        max-width: 460px;
        margin: 2rem auto;
        background: #fff;
        border: 1px solid var(--line);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        }
        .auth-head h2{ color:#b45309; }
        .hint{ color: color-mix(in oklab, var(--ink) 55%, transparent); }

        /* Form tweaks */
        .form-control{
        background: #fff;
        border-radius: .85rem;
        border-color: var(--line);
        padding: .85rem 1rem;
        }
        .form-control:focus{
        border-color: var(--primary-200);
        box-shadow: 0 0 0 .35rem rgba(var(--bs-primary-rgb), .18);
        }
        .form-text.error{ color: #d92d20; }

        .btn-primary{
        border-radius: .9rem;
        box-shadow: var(--shadow);
        }
        .btn-primary:hover{ background-color: var(--primary-600); border-color: var(--primary-600); }

        .divider{
        display:flex; align-items:center; gap:.75rem;
        }
        .divider .line{ height:1px; background:var(--line); flex:1; }
        .divider .text{ font-size:.8rem; color: color-mix(in oklab, var(--ink) 45%, transparent); }

        .foot{ color: color-mix(in oklab, var(--ink) 55%, transparent); font-size:.8rem; }
    </style>
    </head>
    <body>

    <div class="container-fluid auth-grid">
        <div class="row h-100">
        {{-- Left: Hero --}}
        <div class="col-lg-6 d-none d-lg-flex hero align-items-center">
            <div class="hero-inner">
            <div class="logo-badge">
                {{-- stall icon --}}
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#fff" viewBox="0 0 24 24">
                <path d="M3 7.5 6 3h12l3 4.5v2.25A2.25 2.25 0 0 1 18.75 12h-.75v7.5a.75.75 0 0 1-.75.75H6.75a.75.75 0 0 1-.75-.75V12H5.25A2.25 2.25 0 0 1 3 9.75V7.5Zm3 0h12l-1.8 2.7a1.5 1.5 0 0 1-1.25.8H9.05a1.5 1.5 0 0 1-1.25-.8L6 7.5Z"/>
                </svg>
            </div>
            <h1 class="display-6 hero-title">ระบบจองล็อกตลาดนัด</h1>
            <p class="mt-2 hero-sub">
                เข้าสู่ระบบเพื่อจัดการการจอง ตรวจสอบสถานะ และชำระเงินได้สะดวก รวดเร็ว
            </p>
            </div>
        </div>

        {{-- Right: Form --}}
        <div class="col-lg-6 d-flex align-items-center justify-content-center py-4 py-lg-0">
            <div class="auth-card w-100 px-4 px-sm-5 py-4">
            <div class="auth-head mb-3">
                <h2 class="h4 mb-1">เข้าสู่ระบบ</h2>
                <div class="hint small">ยินดีต้อนรับกลับมา</div>
            </div>

            {{-- Session Status --}}
            @if (session('status'))
                <div class="alert alert-warning py-2 mb-3">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="row g-3">
                @csrf

                {{-- Email --}}
                <div class="col-12">
                <label for="email" class="form-label">อีเมล</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                        autocomplete="username" placeholder="you@example.com" class="form-control">
                @error('email')
                    <div class="form-text error">{{ $message }}</div>
                @enderror
                </div>

                {{-- Password --}}
                <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <label for="password" class="form-label mb-0">รหัสผ่าน</label>
                    @if (Route::has('password.request'))
                    <a class="link-underline link-underline-opacity-0" href="{{ route('password.request') }}">ลืมรหัสผ่าน?</a>
                    @endif
                </div>
                <input id="password" type="password" name="password" required
                        autocomplete="current-password" placeholder="••••••••" class="form-control">
                @error('password')
                    <div class="form-text error">{{ $message }}</div>
                @enderror
                </div>

                {{-- Remember me --}}
                <div class="col-12">
                <div class="form-check">
                    <input id="remember_me" class="form-check-input" type="checkbox" name="remember">
                    <label class="form-check-label" for="remember_me">จดจำฉันไว้</label>
                </div>
                </div>

                {{-- Submit --}}
                <div class="col-12">
                <button class="btn btn-primary w-100 py-2" type="submit">เข้าสู่ระบบ</button>
                </div>

                {{-- Divider --}}
                <div class="col-12">
                <div class="divider"><span class="line"></span><span class="text">หรือ</span><span class="line"></span></div>
                </div>

                {{-- Register --}}
                @if (Route::has('register'))
                <div class="col-12 text-center">
                <span class="hint small">ยังไม่มีบัญชี?</span>
                <a href="{{ route('register') }}" class="ms-1">สมัครใช้งาน</a>
                </div>
                @endif
            </form>

            <div class="text-center mt-3 foot">© {{ date('Y') }} ระบบจองล็อกตลาดนัด</div>
            </div>
        </div>
        </div>
    </div>

  {{-- Bootstrap JS (CDN) --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
