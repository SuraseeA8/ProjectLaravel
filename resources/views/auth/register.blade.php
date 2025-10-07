<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- ชื่อจริง --}}
        <div>
            <x-input-label for="users_fname" :value="__('First Name')" />
            <x-text-input id="users_fname" class="block mt-1 w-full" type="text" name="users_fname" :value="old('users_fname')" required autofocus />
            <x-input-error :messages="$errors->get('users_fname')" class="mt-2" />
        </div>

        {{-- นามสกุล --}}
        <div class="mt-4">
            <x-input-label for="users_lname" :value="__('Last Name')" />
            <x-text-input id="users_lname" class="block mt-1 w-full" type="text" name="users_lname" :value="old('users_lname')" required />
            <x-input-error :messages="$errors->get('users_lname')" class="mt-2" />
        </div>

        {{-- อีเมล --}}
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- โทรศัพท์ --}}
        <div class="mt-4">
            <x-input-label for="phone" :value="__('Phone')" />
            <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" required />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        {{-- ชื่อร้าน --}}
        <div class="mt-6">
            <x-input-label for="shop_name" :value="__('Shop Name')" />
            <x-text-input id="shop_name" class="block mt-1 w-full" type="text" name="shop_name" :value="old('shop_name')" required />
            <x-input-error :messages="$errors->get('shop_name')" class="mt-2" />
        </div>

        {{-- รายละเอียดร้าน --}}
        <div class="mt-4">
            <x-input-label for="description" :value="__('Shop Description')" />
            <textarea id="description" name="description" class="block mt-1 w-full" rows="3" maxlength="255">{{ old('description') }}</textarea>
            <x-input-error :messages="$errors->get('description')" class="mt-2" />
        </div>

        {{-- รหัสผ่าน --}}
        <div class="mt-6">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- ยืนยันรหัสผ่าน --}}
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        {{-- ปุ่ม --}}
        <div class="flex items-center justify-end mt-6">
            <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

    <style>
        /* โหลดฟอนต์ Kanit แล้วบังคับใช้ทั้งหน้า */
        @import url('https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700;800&display=swap');
        html, body { font-family: 'Kanit', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans Thai", sans-serif; }
        *, *::before, *::after { font-family: inherit; }

        /* ===== Theme Vars (จับที่ฟอร์มเพื่อไม่ไปรบกวนหน้าอื่น) ===== */
        form{
            --brand:#E68F36;
            --brand-2:#d97f2e;
            --text:#222;
            --muted:#6b7280;
            --line:#ffd8ae;
            --card:#ffffff;
            --bg1:#f1a353;
            --bg2:#f8d29e;
        }

        /* ฉากหลังเต็มหน้าจอ + จัดฟอร์มกึ่งกลาง โดยไม่แก้ Blade */
        html,body{height:100%}
        body{
            margin:0;
            min-height:100svh;
            background:linear-gradient(135deg, var(--bg1), var(--bg2));
            display:flex; align-items:center; justify-content:center;
            color:var(--text);
            -webkit-font-smoothing:antialiased; -moz-osx-font-smoothing:grayscale;
        }

        /* กล่องฟอร์ม (จับ <form> ตรง ๆ) */
        form{
            width:520px; max-width:94vw;
            background:var(--card);
            border:1.6px solid var(--line);
            border-radius:18px;
            padding:26px 26px 24px;
            box-shadow:0 22px 40px rgba(0,0,0,.12);
            box-sizing:border-box;
            position:relative;
        }

        /* หัวฟอร์มสร้างด้วย CSS */
        form::before{
            content:"สมัครสมาชิก";
            display:block; text-align:center;
            font-weight:800; font-size:24px; color:#1b1b1b;
            margin-bottom:12px;
        }

        /* ระยะแนวตั้งยูทิลิตี้ (รองรับคลาสที่ Jetstream ใส่มา) */
        .mt-1{ margin-top:.25rem }
        .mt-2{ margin-top:.5rem }
        .mt-4{ margin-top:1rem }
        .mt-6{ margin-top:1.5rem }
        .ms-4{ margin-left:0 } /* เดี๋ยวเราทำปุ่มเต็มบรรทัดแทน */

        /* label */
        form label{
            display:block; font-weight:700; font-size:14px; color:#333;
            margin-bottom:6px;
        }

        /* inputs / textarea (ครอบคลุม x-text-input) */
        form input[type="text"],
        form input[type="email"],
        form input[type="password"],
        form input[type="tel"],
        form input[type="number"],
        form textarea, form select{
            width:100%;
            box-sizing:border-box;
            padding:11px 12px;
            border-radius:10px;
            border:2px solid #f3d7b8;
            background:#fff; color:var(--text);
            outline:0;
            font-size:14px;
            transition:border-color .18s, box-shadow .18s, background .18s;
        }
        form input:focus,
        form textarea:focus,
        form select:focus{
            border-color:var(--brand);
            box-shadow:0 0 0 4px rgba(230,143,54,.18);
        }

        /* กัน autofill เหลืองของ Chrome */
        input:-webkit-autofill{
            -webkit-box-shadow:0 0 0 1000px #fff inset;
            -webkit-text-fill-color:var(--text);
            transition:background-color 5000s ease-in-out 0s;
        }

        /* แสดง error ของ <x-input-error> ให้ชัด */
        [class*="mt-2"] > span,
        .text-sm.text-red-600{
            color:#b91c1c !important; font-weight:600;
        }

        /* แถวลิงก์ + ปุ่มส่งฟอร์ม */
        .flex.items-center.justify-end.mt-6{
            display:block !important;
            margin-top:16px !important;
        }
        .flex.items-center.justify-end.mt-6 > a{
            display:inline-block;
            color:var(--brand);
            text-decoration:none;
            font-weight:700;
        }
        .flex.items-center.justify-end.mt-6 > a:hover{ text-decoration:underline; }

        /* ปุ่ม Register (x-primary-button เรนเดอร์เป็น <button>) */
        .flex.items-center.justify-end.mt-6 > .ms-4,
        .flex.items-center.justify-end.mt-6 button{
            display:block;
            width:100%;
        }
        .flex.items-center.justify-end.mt-6 button{
            background:linear-gradient(180deg, var(--brand), var(--brand-2));
            color:#fff; border:none; border-radius:12px;
            padding:12px 18px; font-weight:800; letter-spacing:.2px;
            box-shadow:0 12px 22px rgba(230,143,54,.30);
            transition:transform .12s ease, box-shadow .2s, filter .2s;
            cursor:pointer;
            margin-top:12px;   /* ให้ปุ่มห่างจากลิงก์ "Already registered?" */
        }
        .flex.items-center.justify-end.mt-6 button:hover{
            transform:translateY(-1px);
            box-shadow:0 16px 26px rgba(230,143,54,.36);
            filter:saturate(1.04);
        }
    </style>
</x-guest-layout>
