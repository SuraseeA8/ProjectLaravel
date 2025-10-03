<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


use App\Models\Stall;
use App\Models\Stall_Status;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Status;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VendorController extends Controller
{
    /* ---------------------------
     * หน้าแรกผู้ขาย / สรุปย่อ
     * --------------------------- */
    public function showHome(Request $request)
    {
        $ym = $this->resolveYearMonth($request);
        $y = $ym['year']; $m = $ym['month'];

        $user = Auth::user();

        $stats = [
            'bookings_total'   => Booking::where('user_id', $user->id)->count(),
            'bookings_pending' => Booking::where('user_id', $user->id)->where('status_id', Status::PENDING)->count(),
            'bookings_approved'=> Booking::where('user_id', $user->id)->where('status_id', Status::UNAVAILABLE)->count(),
        ];

        return view('vendor.home', compact('stats','y','m'));
    }

    /* ---------------------------
     * โปรไฟล์
     * --------------------------- */
    public function showProfile()
    {
        return view('vendor.profile', ['user' => Auth::user()]);
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:150',
            'email' => 'required|email:rfc,dns|max:190|unique:users,email,' . Auth::id(),
        ]);

        /** @var User $user */
        $user = Auth::user();
        $user->update($data);

        return back()->with('ok', 'อัปเดตโปรไฟล์เรียบร้อย');
    }

    /* ---------------------------
     * รายการล็อก (แสดงกริดตามเดือน)
     * --------------------------- */
    public function stallList(Request $request)
    {
        // รับ year/month เป็น ค.ศ. ตรง ๆ
        $ym = $this->resolveYearMonth($request);
        $y  = (int) $ym['year'];
        $m  = (int) $ym['month'];

        // ดึงสถานะรายเดือนทั้งหมด -> keyBy(stall_id)
        $statuses = \App\Models\Stall_Status::query()
            ->select(['stall_id','status_id'])
            ->where('year', $y)->where('month', $m)
            ->get()->keyBy('stall_id');

        // ดึงล็อกที่ใช้งาน + โซน แล้วแม็พสถานะ
        $stalls = \App\Models\Stall::with('zone')
            ->where('is_active', true)
            ->orderByRaw('zone_id, stall_code')
            ->get()
            ->map(function ($s) use ($statuses) {
                $st  = $statuses->get($s->stall_id);
                $sid = $st->status_id ?? \App\Models\Status::AVAILABLE;
                return [
                    'stall'       => $s,
                    'status_id'   => $sid,
                    'status_name' => match ($sid) {
                        \App\Models\Status::AVAILABLE   => 'ว่าง',
                        \App\Models\Status::UNAVAILABLE => 'ไม่ว่าง',
                        \App\Models\Status::PENDING     => 'รออนุมัติ',
                        \App\Models\Status::CLOSED      => 'ปิดให้จอง',
                    },
                ];
            });

        return view('vendor.stalls', compact('stalls','y','m'));
    }

    /* ---------------------------
     * รายละเอียดล็อก + ปฏิทินสถานะ
     * --------------------------- */
    public function stallDetail(Request $request, Stall $stall)
    {
        $ym = $this->resolveYearMonth($request);
        $y = $ym['year']; $m = $ym['month'];

        $monthStatus = Stall_Status::with('status')
            ->where('stall_id', $stall->stall_id)
            ->where('year', $y)->where('month', $m)->first();

        return view('vendor.stalls.show', compact('stall','monthStatus','y','m'));
    }

    /* ---------------------------
     * ทำการจอง (PENDING)
     * --------------------------- */
    public function bookStall(Request $request, Stall $stall)
    {
        $this->authorizeActiveStall($stall);

        $data = $request->validate([
            'year'  => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|min:1|max:12',
        ]);
        $y = (int)$data['year']; 
        $m = (int)$data['month'];

        // ✅ ผู้ใช้คนนี้มีการจองในเดือนนี้ไปแล้วหรือยัง?
        $already = Booking::where('user_id', Auth::id())
            ->where('year', $y)
            ->where('month', $m)
            ->whereIn('status_id', [Status::PENDING, Status::UNAVAILABLE]) // นับเฉพาะที่ “ยังคงค้าง”
            ->exists();

        if ($already) {
            return back()->withErrors([
                'month' => 'คุณจองได้ 1 ล็อกต่อเดือนเท่านั้น (มีรายการในเดือนนี้อยู่แล้ว)',
            ])->withInput();
        }

        // กันชน: ล็อกนี้เดือนนี้ไม่ว่าง / รออนุมัติ / ปิด
        $busy = Stall_Status::where('stall_id', $stall->stall_id)
            ->where('year', $y)->where('month', $m)
            ->whereIn('status_id', [Status::UNAVAILABLE, Status::PENDING, Status::CLOSED])
            ->exists();

        if ($busy) {
            return back()->withErrors(['month' => 'เดือนนี้ล็อกนี้ไม่ว่างแล้ว'])->withInput();
        }
        try {
            DB::transaction(function () use ($stall, $y, $m) {
                $booking = Booking::create([
                    'user_id'   => Auth::id(),
                    'stall_id'  => $stall->stall_id,
                    'year'      => $y,
                    'month'     => $m,
                    'status_id' => Status::PENDING,
                ]);

                // เผื่อยังไม่มี trigger sync
                Stall_Status::updateOrCreate(
                    ['stall_id' => $stall->stall_id, 'year' => $y, 'month' => $m],
                    [
                        'status_id'  => Status::PENDING,
                        'booking_id' => $booking->booking_id,
                        'user_id'    => Auth::id(),
                        'reason'     => 'รอยืนยันสลิป',
                        'updated_at' => now(),
                    ]
                );
            });
        } catch (\Illuminate\Database\QueryException $ex) {
            // ล็อกซ้ำ / user+month ซ้ำ
            return back()->withErrors(['month' => 'คุณมีการจองในเดือนนี้อยู่แล้ว (1 คน/เดือน จองได้ 1 ล็อก)'])->withInput();
        }

        return redirect()->route('vendor.booking.status')->with('ok', 'ยื่นจองสำเร็จ กรุณาอัปโหลดสลิปเพื่อยืนยัน');
    }


    /* ---------------------------
     * สถานะการจองของฉัน
     * --------------------------- */
    public function bookingStatus(Request $request)
    {
        $ym = $this->resolveYearMonth($request);
        $y  = (int)$ym['year'];
        $m  = (int)$ym['month'];

        $range = $request->input('range', 'month'); // 'month' | 'all'

        $q = Booking::with(['stall.zone','status'])->where('user_id', Auth::id());

        if ($range === 'month') {
            $q->where('year', $y)->where('month', $m);
        }

        $items = $q->orderByDesc('created_at')->paginate(12)->appends(['range' => $range, 'year' => $y, 'month' => $m]); // ให้ paginator จำพารามิเตอร์

        return view('vendor.bookings.index', compact('items','range','y','m'));
    }


    /* ---------------------------
     * ยกเลิกใบจอง (ของฉันเท่านั้น) -> คืนสถานะเป็น AVAILABLE
     * --------------------------- */
    public function cancelBooking(Request $request, Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }
        if ($booking->status_id !== Status::PENDING) {
            return back()->withErrors(['cancel' => 'อนุมัติแล้วหรือยกเลิกไปแล้ว ยกเลิกไม่ได้']);
        }

        DB::transaction(function () use ($booking) {
            $booking->update(['status_id' => Status::AVAILABLE]);

            // ถ้าไม่มีทริกเกอร์ AFTER UPDATE ให้ sync เอง
            Stall_Status::where('stall_id', $booking->stall_id)
                ->where('year', $booking->year)->where('month', $booking->month)
                ->update([
                    'status_id'  => Status::AVAILABLE,
                    'reason'     => 'ผู้ใช้ยกเลิก',
                    'booking_id' => null,
                    'updated_at' => now(),
                ]);
        });

        return back()->with('ok', 'ยกเลิกใบจองเรียบร้อย');
    }

    /* ---------------------------
     * อัปโหลดสลิป: ฟอร์ม + บันทึก
     * --------------------------- */
    public function uploadSlipForm(Request $request, Booking $booking)
    {
        $this->authorizeBookingOwner($booking);
        if ($booking->status_id !== Status::PENDING) {
            return back()->withErrors(['slip' => 'อัปโหลดได้เฉพาะใบจองที่รออนุมัติ']);
        }
        return view('vendor.bookings.upload-slip', compact('booking'));
    }

    public function storeSlip(Request $request, Booking $booking)
    {
        $this->authorizeBookingOwner($booking);

        $data = $request->validate([
            'amount' => 'nullable|numeric|min:0',
            'slip'   => 'required|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        $path = $request->file('slip')->store('slips', 'public');

        $booking->payments()->create([
            'amount'    => $data['amount'] ?? null,
            'slip_path' => $path,
            'mime'      => $request->file('slip')->getClientMimeType(),
        ]);

        return redirect()->route('vendor.booking.status')->with('ok', 'อัปโหลดสลิปเรียบร้อย รอแอดมินตรวจสอบ');
    }

    /* ---------------------------
     * กระดานข่าว/กิจกรรม
     * --------------------------- */
    public function eventBoard()
    {
        // ถ้ามี Model Event ก็ใช้ Event::latest()->paginate(10)
        $events = DB::table('event')->orderByDesc('start_date')->paginate(10);
        return view('vendor.events.index', compact('events'));
    }

    /* ---------------------------
     * helpers
     * --------------------------- */
    private function resolveYearMonth(Request $request): array
    {
        $now = Carbon::now('Asia/Bangkok');
        return [
            'year'  => (int)($request->input('year')  ?? $now->year),
            'month' => (int)($request->input('month') ?? $now->month),
        ];
    }

    private function authorizeActiveStall(Stall $stall): void
    {
        if (! $stall->is_active) {
            abort(403, 'ล็อกนี้ปิดใช้งาน');
        }
    }

    private function authorizeBookingOwner(Booking $booking): void
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }
    }
}