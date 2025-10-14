<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{

    public function index()
    {
        $events = Event::orderBy('start_date', 'desc')->get();
        return view('events.index', compact('events')); 
    }


    public function adminIndex()
    {
        $events = Event::orderBy('start_date', 'desc')->get();
        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'      => ['required','string','max:45'],
            'detail'     => ['required','string','max:255'],
            'start_date' => ['required','date'],
            'end_date'   => ['required','date','after_or_equal:start_date'],
            'img_path'   => ['nullable','image','mimes:jpg,jpeg,png','max:4096'],
        ]);

        if ($request->hasFile('img_path')) {
            $data['img_path'] = $request->file('img_path')->store('events', 'public');
        }

        Event::create($data);

        return redirect()->route('admin.events.index')->with('success', 'เพิ่มประกาศสำเร็จ');
    }

    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $data = $request->validate([
            'title'      => ['required','string','max:45'],
            'detail'     => ['required','string','max:255'],
            'start_date' => ['required','date'],
            'end_date'   => ['required','date','after_or_equal:start_date'],
            'img_path'   => ['nullable','image','mimes:jpg,jpeg,png','max:4096'],
        ]);

        if ($request->hasFile('img_path')) {
            if ($event->img_path && Storage::disk('public')->exists($event->img_path)) {
                Storage::disk('public')->delete($event->img_path);
            }
            $data['img_path'] = $request->file('img_path')->store('events', 'public');
        } else {
            $data['img_path'] = $event->img_path;
        }

        $event->update($data);

        return redirect()->route('admin.events.index')->with('success', 'อัปเดตกิจกรรมเรียบร้อย');
    }

    public function destroy(Event $event)
    {
        if ($event->img_path && Storage::disk('public')->exists($event->img_path)) {
            Storage::disk('public')->delete($event->img_path);
        }
        $event->delete();

        return redirect()->route('admin.events.index')->with('success', 'ลบประกาศสำเร็จ');
    }

    public function board()
    {
        $events = Event::orderBy('start_date', 'desc')->get();
        return view('board', compact('events'));
    }
}