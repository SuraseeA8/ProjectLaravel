<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;



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
        return view('vendor.home');
    }

    /* ---------------------------
     * โปรไฟล์
     * --------------------------- */
    public function showProfile()
    {
        $user = Auth::user();

        // ถ้ามีข้อมูลร้านด้วย (shop_details)
        $shop = DB::table('shop_detail')
            ->where('user_id', $user->id)
            ->whereNull('deleted_at')
            ->first();

        return view('vendor.profile', compact('user', 'shop'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'users_fname' => ['required','string','max:100'],
            'users_lname' => ['required','string','max:100'],
            'email'       => ['required','email','max:255', Rule::unique('users','email')->ignore($user->id)],
            'phone'       => ['nullable','string','max:30'],
        ]);

        DB::table('users')->where('id', $user->id)->update([
            'users_fname' => $validated['users_fname'],
            'users_lname' => $validated['users_lname'],
            'email'       => $validated['email'],
            'phone'       => $validated['phone'] ?? null,
            'updated_at'  => now(),
        ]);

        return back()->with('success', 'บันทึกข้อมูลผู้ใช้เรียบร้อยแล้ว');
    }

    public function updateShop(Request $request)
    {
        $data = $request->validate([
            'shop_name'   => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
        ]);

        DB::table('shop_detail')->updateOrInsert(
            ['user_id' => Auth::id()],
            [
                'shop_name'   => $data['shop_name'],
                'description' => $data['description'] ?? null,
            ]
        );

        return back()->with('success', 'บันทึกข้อมูลร้านค้าแล้ว');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:8|confirmed',
        ]);

        // ดึง hash เดิมจากฐานข้อมูล (ไม่พึ่ง $user->password)
        $hashed = DB::table('users')->where('id', Auth::id())->value('password');

        if (! Hash::check($request->current_password, $hashed)) {
            return back()->withErrors(['current_password' => 'รหัสผ่านเดิมไม่ถูกต้อง'])
                        ->with('show_password_panel', true);
        }

        DB::table('users')->where('id', Auth::id())->update([
            'password' => Hash::make($request->new_password),
            // ถ้าไม่มี updated_at ในตาราง อย่าใส่บรรทัดนี้
            // 'updated_at' => now(),
        ]);

        return back()->with('success', 'เปลี่ยนรหัสผ่านเรียบร้อยแล้ว');
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
                        \App\Models\Status::CANCEL     => 'ยกเลิก',
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
        $y  = (int) $ym['year'];
        $m  = (int) $ym['month'];

        // สถานะของล็อกในเดือน/ปีนี้
        $monthStatus = Stall_Status::with('status')
            ->where('stall_id', $stall->stall_id)
            ->where('year', $y)->where('month', $m)
            ->first();

        // ผู้ใช้นี้มีใบจองเดือนนี้อยู่แล้วไหม (นับเฉพาะ active ถ้าคุณอยากให้ยกเลิกแล้วจองใหม่ได้)
        $hasMyBookingThisMonth = Booking::where('user_id', Auth::id())
            ->where('year', $y)->where('month', $m)
            ->whereIn('status_id', [Status::PENDING, Status::UNAVAILABLE]) // นับเฉพาะที่ยังล็อกอยู่
            ->exists();

        // ตัดสินใจว่าจองได้ไหม และเหตุผล
        $canBook = true;
        $cannotReason = null;

        if (! $stall->is_active) {
            $canBook = false; $cannotReason = 'ล็อกนี้ปิดใช้งาน';
        } elseif ($hasMyBookingThisMonth) {
            $canBook = false; $cannotReason = 'คุณมีการจองในเดือนนี้อยู่แล้ว (1 คน/เดือน จองได้ 1 ล็อก)';
        } elseif ($monthStatus && in_array($monthStatus->status_id, [Status::UNAVAILABLE, Status::PENDING, Status::CLOSED])) {
            $canBook = false; $cannotReason = 'เดือนนี้ล็อกนี้ไม่ว่าง/รออนุมัติ/ปิดให้จอง';
        }

        return view('vendor.stall_detail', compact('stall','monthStatus','y','m','canBook','cannotReason'));
    }


    /* ---------------------------
     * ทำการจอง (PENDING)
     * --------------------------- */
    public function bookStall(Request $request, Stall $stall)
    {
        $this->authorizeActiveStall($stall);

        $data = $request->validate([
            'year'   => 'required|integer|min:2000|max:2100',
            'month'  => 'required|integer|min:1|max:12',
            'amount' => 'nullable|numeric|min:0',
            'slip'   => 'required|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);
        $y = (int) $data['year'];
        $m = (int) $data['month'];
        $uid = Auth::id();

        // กันล็อกใหม่เดือนนี้: ต้องไม่ถูกจอง/รอ/ปิด
        $busy = Stall_Status::where('stall_id', $stall->stall_id)
            ->where('year', $y)->where('month', $m)
            ->whereIn('status_id', [Status::UNAVAILABLE, Status::PENDING, Status::CLOSED])
            ->exists();
        if ($busy) {
            return back()->withErrors(['month' => 'เดือนนี้ล็อกนี้ไม่ว่างแล้ว'])->withInput();
        }

        // เก็บไฟล์สลิปไว้ก่อน (คุณจะผูกกับ payment ทีหลังก็ได้)
        $path = $request->file('slip')->store('slips', 'public');

        DB::transaction(function () use ($uid, $stall, $y, $m) {
            // หา booking เดิมของ user เดือนนี้ (ถ้ามี)
            $existing = Booking::where('user_id', $uid)
                ->where('year', $y)->where('month', $m)
                ->lockForUpdate()
                ->first();

            // ถ้ามี booking เดิม -> ปลดสถานะล็อกเดิมให้ AVAILABLE
            if ($existing && $existing->stall_id !== $stall->stall_id) {
                Stall_Status::where('stall_id', $existing->stall_id)
                    ->where('year', $y)->where('month', $m)
                    ->update([
                        'status_id'  => Status::AVAILABLE,
                        'booking_id' => null,
                        'user_id'    => null,
                        'reason'     => 'สลับไปจองล็อกอื่น',
                        'updated_at' => now(),
                    ]);
            }

            // อัปเดตหรือสร้าง booking ของ user เดือนนี้ (คีย์ไม่ซ้ำ: user_id, year, month)
            $booking = Booking::updateOrCreate(
                ['user_id' => $uid, 'year' => $y, 'month' => $m],
                ['stall_id' => $stall->stall_id, 'status_id' => Status::PENDING]
            );

            // ตั้งสถานะล็อกใหม่เป็น PENDING และผูก booking นี้
            Stall_Status::updateOrCreate(
                ['stall_id' => $stall->stall_id, 'year' => $y, 'month' => $m],
                [
                    'status_id'  => Status::PENDING,
                    'booking_id' => $booking->booking_id,
                    'user_id'    => $uid,
                    'reason'     => 'รอยืนยันสลิป',
                    'updated_at' => now(),
                ]
            );
        });

        return redirect()->route('vendor.booking.status')
            ->with('ok', 'ยื่นจองสำเร็จ (อัปเดตใบจองเดือนนี้ให้ล็อกนี้แล้ว) กรุณารอการตรวจสอบ');
    }



    /* ---------------------------
     * สถานะการจองของฉัน
     * --------------------------- */
    public function bookingStatus(Request $request)
    {
        $items = Booking::with(['stall.zone','status','payments'])
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('vendor.booking_status', compact('items'));
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
        return view('vendor.upload_slip.blade', compact('booking'));
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

    

    public function checkoutForm(Request $request, Stall $stall)
    {
        $y = (int) $request->query('year');
        $m = (int) $request->query('month');

        // ตรวจสิทธิ์เข้า checkout (กดดูได้เฉพาะที่จองได้จริง)
        $monthStatus = Stall_Status::where('stall_id', $stall->stall_id)
            ->where('year', $y)->where('month', $m)->first();

        $hasMyBookingThisMonth = Booking::where('user_id', Auth::id())
            ->where('year', $y)->where('month', $m)
            ->whereIn('status_id', [Status::PENDING, Status::UNAVAILABLE])
            ->exists();

        $canBook = $stall->is_active
            && ! $hasMyBookingThisMonth
            && ! ($monthStatus && in_array($monthStatus->status_id, [
                Status::UNAVAILABLE, Status::PENDING, Status::CLOSED
            ]));

        if (! $canBook) {
            return redirect()->route('vendor.stall.detail', $stall->stall_id)
                ->withErrors(['slip' => 'เงื่อนไขการจองไม่ผ่าน'])->withInput();
        }

        return view('vendor.upload_slip', compact('stall','y','m'));
    }

    public function checkoutSubmit(Request $request, Stall $stall)
    {
        $data = $request->validate([
            'year'         => 'required|integer|min:2000|max:2100',
            'month'        => 'required|integer|min:1|max:12',
            'acc_name'     => 'required|string|max:150',
            'bank'         => 'required|string|max:100',
            'payment_date' => 'required|date',
            'amount'       => 'required|numeric|min:0.01',
            'slip'         => 'required|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);
        $y = (int) $data['year'];
        $m = (int) $data['month'];
        $uid = Auth::id();

        // ล็อกนี้เดือนนี้ต้องว่างจริง
        $busy = Stall_Status::where('stall_id', $stall->stall_id)
            ->where('year', $y)->where('month', $m)
            ->whereIn('status_id', [Status::UNAVAILABLE, Status::PENDING, Status::CLOSED])
            ->exists();
        if ($busy || ! $stall->is_active) {
            return back()->withErrors(['month' => 'เดือนนี้ล็อกนี้ไม่ว่างแล้ว'])->withInput();
        }

        // เก็บไฟล์สลิป
        $path = $request->file('slip')->store('slips', 'public');

        DB::transaction(function () use ($uid, $stall, $y, $m, $data, $path) {
            // หา booking เดิมเดือนนี้
            $existing = Booking::where('user_id', $uid)
                ->where('year', $y)->where('month', $m)
                ->lockForUpdate()
                ->first();

            // ถ้ามีและล็อกเดิม != ล็อกใหม่ -> ปลดล็อกเดิม
            if ($existing && $existing->stall_id !== $stall->stall_id) {
                Stall_Status::where('stall_id', $existing->stall_id)
                    ->where('year', $y)->where('month', $m)
                    ->update([
                        'status_id'  => Status::AVAILABLE,
                        'booking_id' => null,
                        'user_id'    => null,
                        'reason'     => 'สลับไปจองล็อกอื่น',
                        'updated_at' => now(),
                    ]);
            }

            // อัปเดต/สร้าง booking เดิมเดือนนี้ -> ผูกล็อกใหม่ + PENDING
            $booking = Booking::updateOrCreate(
                ['user_id' => $uid, 'year' => $y, 'month' => $m],
                ['stall_id' => $stall->stall_id, 'status_id' => Status::PENDING]
            );

            // แนบสลิป (อัปเดต/สร้างรายการจ่ายใหม่)
            $booking->payments()->create([
                'acc_name'     => $data['acc_name'],
                'bank'         => $data['bank'],
                'payment_date' => $data['payment_date'],
                'amount'       => $data['amount'],
                'slip_path'    => $path,
            ]);

            // ทำให้สถานะล็อกใหม่เป็น PENDING
            Stall_Status::updateOrCreate(
                ['stall_id' => $stall->stall_id, 'year' => $y, 'month' => $m],
                [
                    'status_id'  => Status::PENDING,
                    'booking_id' => $booking->booking_id,
                    'user_id'    => $uid,
                    'reason'     => 'รอยืนยันสลิป',
                    'updated_at' => now(),
                ]
            );
        });

        return redirect()->route('vendor.booking.status', [
            'range' => 'month',
            'year'  => $y,
            'month' => $m,
        ])->with('ok', 'ยืนยันการจองเรียบร้อย (ปรับใบจองเดือนนี้ให้ล็อกนี้แล้ว) รอแอดมินตรวจสอบ');
    }

}