<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ShopDetail;
use Illuminate\Support\Facades\DB;



class UserController extends Controller
{
    use HasFactory;

    public function index()
    {
        // เมื่อโมเดลใช้ SoftDeletes แล้ว การเรียก all() จะไม่ดึงข้อมูลที่ถูกลบ (deleted_at != null)
        $users = User::all();
        $shops = ShopDetail::all();

        return view('usermanage', compact('users', 'shops'));
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            // ลบข้อมูลร้าน (soft delete) ที่ผูกกับ user นี้ก่อน
            ShopDetail::where('user_id', $id)->delete();

            // ลบผู้ใช้ (soft delete)
            User::where('id', $id)->delete();
        });

        return redirect()->route('admin.users.index')->with('ok','ลบผู้ใช้เรียบร้อย');
    }
    
}