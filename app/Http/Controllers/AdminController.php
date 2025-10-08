<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use App\Models\Stall;

class AdminController extends Controller
{
    public function showHome()
    {
        return view('admin.home');
    }
}
