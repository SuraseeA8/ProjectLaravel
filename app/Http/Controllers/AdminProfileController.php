<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Validation\Rule;                          
use Illuminate\Validation\Rules\Password as PasswordRule;

class AdminProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        return view('adminprofile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = $request->user(); 

        $request->validate([
            'Users_Fname' => ['required', 'string', 'max:255'],
            'Users_Lname' => ['required', 'string', 'max:255'],
            'Email'       => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone'       => ['nullable', 'regex:/^[0-9]{9,10}$/'],
        ], [], [
            'Users_Fname' => 'ชื่อ',
            'Users_Lname' => 'นามสกุล',
            'Email'       => 'อีเมล',
            'phone'       => 'เบอร์โทร',
        ]);

        $user->users_fname = $request->input('Users_Fname');
        $user->users_lname = $request->input('Users_Lname');
        $user->email       = $request->input('Email');
        $user->phone       = $request->input('phone');

        $wantsPasswordChange = $request->filled('current_password')
            || $request->filled('password')
            || $request->filled('password_confirmation');

        if ($wantsPasswordChange) {
            $request->validate([
                'current_password' => ['required'],
                'password'         => ['required', 'confirmed', PasswordRule::min(8)],
            ], [], [
                'current_password' => 'รหัสผ่านเดิม',
                'password'         => 'รหัสผ่านใหม่',
            ]);

            if (!password_verify($request->input('current_password'), $user->password)) {
                return back()->with('error', ' รหัสผ่านเดิมไม่ถูกต้อง')->withInput();
            }

            if (password_verify($request->input('password'), $user->password)) {
                return back()->withErrors(['password' => 'รหัสผ่านใหม่ห้ามซ้ำกับรหัสผ่านเดิม'])->withInput();
            }

            $user->password = password_hash($request->input('password'), PASSWORD_BCRYPT);
        }

    
        $user->save();

        return back()->with('success', 'บันทึกการเปลี่ยนแปลงเรียบร้อยแล้ว');
    }

}
