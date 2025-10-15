<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class EventvController extends Controller
{
    public function eventBoard()
    {
        $events = DB::table('event')->orderByDesc('start_date')->paginate(10);
        return view('vendor.events.index', compact('events'));
    }
}
