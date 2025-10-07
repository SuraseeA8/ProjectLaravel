<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Validation\Rule;                          // ✅ เพิ่มอันนี้
use Illuminate\Validation\Rules\Password as PasswordRule;

class AdminProfileController extends Controller
{
    // แสดงหน้าโปรไฟล์ (ต้องผ่าน middleware(['auth','can:admin']) ตาม route)
    public function index(Request $request)
    {
        $user = $request->user(); // ผู้ใช้ที่ล็อกอิน
        return view('adminprofile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = $request->user(); // ผู้ใช้ที่ล็อกอิน

        // 1) validate โปรไฟล์
        $request->validate([
            'Users_Fname' => ['required', 'string', 'max:255'],
            'Users_Lname' => ['required', 'string', 'max:255'],
            // อีเมลต้องไม่ซ้ำกับคนอื่น (ยกเว้นตัวเอง)
            'Email'       => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            // phone: ตัวเลข 9–10 หลัก (ปรับเป็น required ถ้าต้องการ)
            'phone'       => ['nullable', 'regex:/^[0-9]{9,10}$/'],
        ], [], [
            'Users_Fname' => 'ชื่อ',
            'Users_Lname' => 'นามสกุล',
            'Email'       => 'อีเมล',
            'phone'       => 'เบอร์โทร',
        ]);

        // 2) อัปเดตโปรไฟล์ (map ชื่อฟิลด์จากฟอร์ม -> คอลัมน์ใน DB)
        $user->users_fname = $request->input('Users_Fname');
        $user->users_lname = $request->input('Users_Lname');
        $user->email       = $request->input('Email');
        $user->phone       = $request->input('phone');

        // 3) ตรวจว่าต้องการเปลี่ยนรหัสผ่านหรือไม่ (กรอกช่องใดช่องหนึ่งมา)
        $wantsPasswordChange = $request->filled('current_password')
            || $request->filled('password')
            || $request->filled('password_confirmation');

        if ($wantsPasswordChange) {
            // validate เฉพาะส่วนรหัสผ่าน
            $request->validate([
                'current_password' => ['required'],
                'password'         => ['required', 'confirmed', PasswordRule::min(8)],
            ], [], [
                'current_password' => 'รหัสผ่านเดิม',
                'password'         => 'รหัสผ่านใหม่',
            ]);

            // 3.1 เช็ค current_password กับ hash ใน DB
            if (!password_verify($request->input('current_password'), $user->password)) {
                return back()->with('error', '❌ รหัสผ่านเดิมไม่ถูกต้อง')->withInput();
            }

            // 3.2 กันรหัสใหม่ซ้ำกับรหัสเดิม
            if (password_verify($request->input('password'), $user->password)) {
                return back()->withErrors(['password' => 'รหัสผ่านใหม่ห้ามซ้ำกับรหัสผ่านเดิม'])->withInput();
            }

            // 3.3 เซ็ต hash ใหม่
            $user->password = password_hash($request->input('password'), PASSWORD_BCRYPT);
        }

        // 4) บันทึก
        $user->save();

        return back()->with('success', '✅ บันทึกการเปลี่ยนแปลงเรียบร้อยแล้ว');
    }

}
