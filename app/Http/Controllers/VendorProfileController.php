<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VendorProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user = Auth::user();

        // ดึงร้านค้าจาก shop_details ของผู้ใช้คนนี้ (ไม่นับที่ถูก soft delete)
        $shop = DB::table('shop_detail')
            ->where('user_id', $user->id)
            ->whereNull('deleted_at')
            ->first();

        return view('vendorprofile', [
            'user' => $user,
            'shop' => $shop,
        ]);
    }

    /** อัปเดตตาราง users */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'users_fname' => ['required', 'string', 'max:100'],
            'users_lname' => ['required', 'string', 'max:100'],
            'email'       => [
                'required', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($user->id, 'id')
            ],
            'phone'       => ['nullable', 'string', 'max:30'],
        ], [
            'users_fname.required' => 'กรุณากรอกชื่อ',
            'users_lname.required' => 'กรุณากรอกนามสกุล',
            'email.required'       => 'กรุณากรอกอีเมล',
            'email.email'          => 'รูปแบบอีเมลไม่ถูกต้อง',
            'email.unique'         => 'อีเมลนี้ถูกใช้งานแล้ว',
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

    /** เปลี่ยนรหัสผ่านในตาราง users */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password'          => ['required'],
            'new_password'              => ['required', 'min:8', 'confirmed'],
            // ต้องมี field new_password_confirmation มาด้วยตามกฎ confirmed
        ], [
            'current_password.required' => 'กรุณากรอกรหัสผ่านเดิม',
            'new_password.required'     => 'กรุณากรอกรหัสผ่านใหม่',
            'new_password.min'          => 'รหัสผ่านใหม่ต้องอย่างน้อย 8 ตัวอักษร',
            'new_password.confirmed'    => 'รหัสผ่านใหม่และยืนยันไม่ตรงกัน',
        ]);

        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()
                ->withErrors(['current_password' => 'รหัสผ่านเดิมไม่ถูกต้อง'])
                ->with('show_password_panel', true);
        }

        DB::table('users')->where('id', $user->id)->update([
            'password'   => Hash::make($request->new_password),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'เปลี่ยนรหัสผ่านเรียบร้อยแล้ว');
    }

    /** อัปเดต/เพิ่มข้อมูลร้านค้าในตาราง shop_details */
    public function updateShop(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'shop_name'   => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
        ], [
            'shop_name.required' => 'กรุณากรอกชื่อร้านค้า',
        ]);

        $existing = DB::table('shop_details')
            ->where('user_id', $user->id)
            ->whereNull('deleted_at')
            ->first();

        if ($existing) {
            // update
            DB::table('shop_details')
                ->where('shop_detail_id', $existing->shop_detail_id)
                ->update([
                    'shop_name'  => $validated['shop_name'],
                    'description'=> $validated['description'] ?? null,
                    'updated_at' => now(),
                ]);
        } else {
            // insert (กำหนด user_id = เจ้าของ)
            DB::table('shop_details')->insert([
                'user_id'    => $user->id,
                'shop_name'  => $validated['shop_name'],
                'description'=> $validated['description'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return back()->with('success', 'บันทึกข้อมูลร้านค้าเรียบร้อยแล้ว');
    }
}
