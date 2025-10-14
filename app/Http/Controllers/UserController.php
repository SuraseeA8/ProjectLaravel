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
        $users = User::all();
        $shops = ShopDetail::all();

        return view('usermanage', compact('users', 'shops'));
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            ShopDetail::where('user_id', $id)->delete();

            User::where('id', $id)->delete();
        });

        return redirect()->route('admin.users.index')->with('ok','ลบผู้ใช้เรียบร้อย');
    }
    
}