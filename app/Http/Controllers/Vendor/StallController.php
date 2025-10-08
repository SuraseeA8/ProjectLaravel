<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\Stall;
use Carbon\Carbon;
use App\Models\Stall_Status;
use App\Models\Status;

class StallController extends Controller
{
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

    private function resolveYearMonth(Request $request): array
    {
        $now = Carbon::now('Asia/Bangkok');
        return [
            'year'  => (int)($request->input('year')  ?? $now->year),
            'month' => (int)($request->input('month') ?? $now->month),
        ];
    }

}
