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
        $ym = $this->resolveYearMonth($request);
        $y = $ym['year']; $m = $ym['month'];

        // สถานะรายเดือนทั้งหมด keyBy stall_id
        $statuses = Stall_Status::with('status')
            ->where('year', $y)->where('month', $m)
            ->get()->keyBy('stall_id');

        $stalls = Stall::with('zone')
            ->where('is_active', true)
            ->orderByRaw('zone_id, stall_code')
            ->get()
            ->map(function ($s) use ($statuses) {
                $st = $statuses->get($s->stall_id);
                $sid = $st->status_id ?? Status::AVAILABLE;
                return [
                    'stall'       => $s,
                    'status_id'   => $sid,
                    'status_name' => match ($sid) {
                        Status::AVAILABLE   => 'ว่าง',
                        Status::UNAVAILABLE => 'ไม่ว่าง',
                        Status::PENDING     => 'รออนุมัติ',
                        Status::CLOSED      => 'ปิดให้จอง',
                    },
                ];
            });

        return view('vendor.stalls.index', compact('stalls','y','m'));
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
        $y = (int)$data['year']; $m = (int)$data['month'];

        // กันชน: ถ้าเดือนนั้นเป็น 2/3/4 = จองไม่ได้
        $busy = Stall_Status::where('stall_id', $stall->stall_id)
            ->where('year', $y)->where('month', $m)
            ->whereIn('status_id', [Status::UNAVAILABLE, Status::PENDING, Status::CLOSED])
            ->exists();

        if ($busy) {
            return back()->withErrors(['month' => 'เดือนนี้ล็อกนี้ไม่ว่างแล้ว'])->withInput();
        }

        DB::transaction(function () use ($stall, $y, $m) {
            $booking = Booking::create([
                'user_id'   => Auth::id(),
                'stall_id'  => $stall->stall_id,
                'year'      => $y,
                'month'     => $m,
                'status_id' => Status::PENDING,
            ]);

            // เผื่อเครื่องคุณยังไม่ได้ตั้ง Trigger AFTER INSERT ให้ upsert ด้วย
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

        return redirect()->route('vendor.booking.status')->with('ok', 'ยื่นจองสำเร็จ กรุณาอัปโหลดสลิปเพื่อยืนยัน');
    }

    /* ---------------------------
     * สถานะการจองของฉัน
     * --------------------------- */
    public function bookingStatus(Request $request)
    {
        $items = Booking::with(['stall.zone','status'])
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('vendor.bookings.index', compact('items'));
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