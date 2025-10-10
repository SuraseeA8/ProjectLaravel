<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class EventvController extends Controller
{
    public function eventBoard()
    {
        // ถ้ามี Model Event ก็ใช้ Event::latest()->paginate(10)
        $events = DB::table('event')->orderByDesc('start_date')->paginate(10);
        return view('vendor.events.index', compact('events'));
    }
}
