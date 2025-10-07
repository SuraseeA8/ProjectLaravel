<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #E68F36, #f5b76d);
            font-family: 'Kanit', sans-serif;
            margin: 0;
            padding: 0;
        }

        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .login-box {
            background: #fff;
            padding: 35px;
            border-radius: 16px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            animation: fadeIn 0.8s ease-in-out;
        }

        .login-box h2 {
            text-align: center;
            margin-bottom: 10px;
            color: #E68F36;
            font-size: 26px;
            font-weight: bold;
        }

        .login-box p {
            text-align: center;
            margin-bottom: 25px;
            color: #666;
            font-size: 14px;
        }

        label {
            font-weight: 600;
            color: #333;
            margin-bottom: 6px;
            display: block;
        }

        input {
            width: 100%;
            padding: 10px 12px;
            border: 1.5px solid #ddd;
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 15px;
            transition: 0.2s;
        }

        input:focus {
            border-color: #E68F36;
            box-shadow: 0 0 6px rgba(230, 143, 54, 0.4);
            outline: none;
        }

        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .remember-me input {
            width: auto;
            margin-right: 8px;
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
        }

        .forgot-link {
            font-size: 13px;
            color: #555;
            text-decoration: underline;
        }

        .forgot-link:hover {
            color: #000;
        }

        .login-btn {
            background: #E68F36;
            color: #fff;
            border: none;
            border-radius: 30px;
            padding: 10px 25px;
            font-weight: bold;
            font-size: 14px;
            cursor: pointer;
            transition: 0.3s;
        }

        .login-btn:hover {
            background: #d77f2e;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-box">
            <h2>เข้าสู่ระบบ</h2>
            <p>กรอกอีเมลและรหัสผ่านของคุณ</p>

            {{-- Session Status --}}
            @if (session('status'))
                <div style="color: green; text-align:center; margin-bottom:10px;">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email -->
                <label for="email">อีเมล</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>

                <!-- Password -->
                <label for="password">รหัสผ่าน</label>
                <input id="password" type="password" name="password" required>

                <!-- Remember Me -->
                <div class="remember-me">
                    <input id="remember_me" type="checkbox" name="remember">
                    <label for="remember_me">จำฉันไว้ในระบบ</label>
                </div>

                <!-- Actions -->
                <div class="form-actions">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-link">ลืมรหัสผ่าน?</a>
                    @endif
                    <button type="submit" class="login-btn">เข้าสู่ระบบ</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>