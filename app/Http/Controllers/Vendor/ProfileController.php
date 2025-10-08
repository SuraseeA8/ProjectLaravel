<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function showProfile()
    {
        $user = Auth::user();
        $shop = DB::table('shop_detail')
            ->where('user_id', $user->id)
            ->whereNull('deleted_at')
            ->first();

        return view('vendor.profile', compact('user', 'shop'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'users_fname' => ['required','string','max:100'],
            'users_lname' => ['required','string','max:100'],
            'email'       => ['required','email','max:255', Rule::unique('users','email')->ignore($user->id)],
            'phone'       => ['nullable','string','max:30'],
        ]);

        DB::table('users')->where('id', $user->id)->update([
            'users_fname' => $validated['users_fname'],
            'users_lname' => $validated['users_lname'],
            'email'       => $validated['email'],
            'phone'       => $validated['phone'] ?? null,
            'updated_at'  => now(),
        ]);

        return back()->with('success', 'บันทึกข้อมูลผู้ใช้เรียบร้อยแล้ว');
    }

    public function updateShop(Request $request)
    {
        $data = $request->validate([
            'shop_name'   => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
        ]);

        DB::table('shop_detail')->updateOrInsert(
            ['user_id' => Auth::id()],
            [
                'shop_name'   => $data['shop_name'],
                'description' => $data['description'] ?? null,
            ]
        );

        return back()->with('success', 'บันทึกข้อมูลร้านค้าแล้ว');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:8|confirmed',
        ]);

        // ดึง hash เดิมจากฐานข้อมูล (ไม่พึ่ง $user->password)
        $hashed = DB::table('users')->where('id', Auth::id())->value('password');

        if (! Hash::check($request->current_password, $hashed)) {
            return back()->withErrors(['current_password' => 'รหัสผ่านเดิมไม่ถูกต้อง'])
                        ->with('show_password_panel', true);
        }

        DB::table('users')->where('id', Auth::id())->update([
            'password' => Hash::make($request->new_password),
            // ถ้าไม่มี updated_at ในตาราง อย่าใส่บรรทัดนี้
            // 'updated_at' => now(),
        ]);

        return back()->with('success', 'เปลี่ยนรหัสผ่านเรียบร้อยแล้ว');
    }
}
