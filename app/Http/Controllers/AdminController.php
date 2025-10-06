<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use App\Models\Stall;

class AdminController extends Controller
{
    public function stalls()
    {
        // $stalls = Stall::orderBy('id')->get(); // ถ้ามีโมเดล
        return view('admin.stalls.index'/*, compact('stalls')*/);
    }

    public function toggleStall($id) { /* ... */ }
    public function cancelStall($id) { /* ... */ }

    public function paymentCheck()
    {
        return view('admin.payments.index');
    }
}
