<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #E68F36, #f5b76d);
            font-family: 'Kanit', sans-serif;
            margin: 0;
            padding: 0;
        }

        .register-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .register-box {
            background: #fff;
            padding: 35px;
            border-radius: 16px;
            width: 100%;
            max-width: 520px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            animation: fadeIn 0.8s ease-in-out;
        }

        .register-box h2 {
            text-align: center;
            margin-bottom: 10px;
            color: #E68F36;
            font-size: 26px;
            font-weight: bold;
        }

        .register-box p {
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

        input,
        textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1.5px solid #ddd;
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 15px;
            transition: 0.2s;
        }

        input:focus,
        textarea:focus {
            border-color: #E68F36;
            box-shadow: 0 0 6px rgba(230, 143, 54, 0.4);
            outline: none;
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
        }

        .login-link {
            font-size: 13px;
            color: #555;
            text-decoration: underline;
        }

        .login-link:hover {
            color: #000;
        }

        .register-btn {
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

        .register-btn:hover {
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
    <div class="register-container">
        <div class="register-box">
            <h2>สมัครสมาชิก</h2>
            <p>กรอกข้อมูลเพื่อสร้างบัญชีของคุณ</p>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                {{-- ชื่อจริง --}}
                <label for="users_fname">ชื่อจริง</label>
                <input type="text" id="users_fname" name="users_fname" value="{{ old('users_fname') }}" required>

                {{-- นามสกุล --}}
                <label for="users_lname">นามสกุล</label>
                <input type="text" id="users_lname" name="users_lname" value="{{ old('users_lname') }}" required>

                {{-- อีเมล --}}
                <label for="email">อีเมล</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>

                {{-- โทรศัพท์ --}}
                <label for="phone">เบอร์โทรศัพท์</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required>

                {{-- ชื่อร้าน --}}
                <label for="shop_name">ชื่อร้าน</label>
                <input type="text" id="shop_name" name="shop_name" value="{{ old('shop_name') }}" required>

                {{-- รายละเอียดร้าน --}}
                <label for="description">รายละเอียดร้าน</label>
                <textarea id="description" name="description" rows="3" maxlength="255">{{ old('description') }}</textarea>

                {{-- รหัสผ่าน --}}
                <label for="password">รหัสผ่าน</label>
                <input type="password" id="password" name="password" required>

                {{-- ยืนยันรหัสผ่าน --}}
                <label for="password_confirmation">ยืนยันรหัสผ่าน</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>

                <div class="form-actions">
                    <a href="{{ route('login') }}" class="login-link">มีบัญชีอยู่แล้ว? เข้าสู่ระบบ</a>
                    <button type="submit" class="register-btn">สมัครสมาชิก</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>