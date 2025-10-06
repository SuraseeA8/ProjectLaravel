<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    /**
     * หน้าแสดงกิจกรรม (เวอร์ชัน public/vendor)
     * เดิมคุณเขียน view('vendor.events.index') ซึ่งไม่มีไฟล์ → เปลี่ยนเป็น events.index
     */
    public function index()
    {
        $events = Event::orderBy('start_date', 'desc')->get();
        return view('events.index', compact('events')); // ✅ resources/views/events/index.blade.php
    }

    /**
     * หน้าแอดมิน: รายการกิจกรรม
     */
    public function adminIndex()
    {
        $events = Event::orderBy('start_date', 'desc')->get();
        return view('admin.events.index', compact('events'));
    }

    /**
     * ฟอร์มสร้างกิจกรรม (แอดมิน)
     */
    public function create()
    {
        return view('admin.events.create');
    }

    /**
     * บันทึกกิจกรรมใหม่ (แอดมิน)
     * - อัปโหลดรูปเก็บที่ storage/app/public/events
     */
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

    /**
     * ฟอร์มแก้ไขกิจกรรม (แอดมิน)
     */
    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    /**
     * อัปเดตข้อมูลกิจกรรม (แอดมิน)
     * - ถ้าอัปโหลดรูปใหม่ จะลบรูปเก่าออกให้
     */
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
            // ลบไฟล์เก่า (ถ้ามี)
            if ($event->img_path && Storage::disk('public')->exists($event->img_path)) {
                Storage::disk('public')->delete($event->img_path);
            }
            $data['img_path'] = $request->file('img_path')->store('events', 'public');
        } else {
            // ถ้าไม่ได้อัปโหลดใหม่ ให้เก็บค่าเดิมไว้
            $data['img_path'] = $event->img_path;
        }

        $event->update($data);

        return redirect()->route('admin.events.index')->with('success', 'อัปเดตกิจกรรมเรียบร้อย');
    }

    /**
     * ลบกิจกรรม (แอดมิน)
     * - ลบไฟล์รูปใน storage ด้วย
     */
    public function destroy(Event $event)
    {
        if ($event->img_path && Storage::disk('public')->exists($event->img_path)) {
            Storage::disk('public')->delete($event->img_path);
        }
        $event->delete();

        return redirect()->route('admin.events.index')->with('success', 'ลบประกาศสำเร็จ');
    }

    /**
     * บอร์ดกิจกรรมหน้า public (/board)
     */
    public function board()
    {
        $events = Event::orderBy('start_date', 'desc')->get();
        return view('board', compact('events'));
    }
}