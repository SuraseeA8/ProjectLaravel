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
        $ym = $this->Ym($request);
        extract($ym);

        $statuses = Stall_Status::query()  //สถานะ
            ->select(['stall_id', 'status_id'])
            ->where('year', $y)->where('month', $m)
            ->get()->keyBy('stall_id');

        $stalls = Stall::with('zone') 
            ->where('is_active', true)
            ->orderByRaw('zone_id, stall_code')
            ->get()
            ->map(function ($s) use ($statuses) {
                $st  = $statuses->get($s->stall_id);   //ดึงสถานะ stallst 
                $sid = $st->status_id ?? Status::AVAILABLE;
                return [
                    'stall'       => $s,
                    'status_id'   => $sid,
                    'status_name' => match ($sid) {  
                        Status::AVAILABLE   => 'ว่าง',
                        Status::UNAVAILABLE => 'ไม่ว่าง',
                        Status::PENDING     => 'รออนุมัติ',
                        Status::CLOSED      => 'ปิดให้จอง',
                        Status::CANCEL     => 'ยกเลิก',
                    },
                ];
            });

        return view('vendor.stalls', compact('stalls','y','m','startMonth'));
    }

    
    public function stallDetail(Request $request, Stall $stall)
    {
        $ym = $this->Ym($request);
        extract($ym);

        $monthStatus = Stall_Status::with('status')
            ->where('stall_id', $stall->stall_id)
            ->where('year', $y)->where('month', $m)
            ->first();

        $BookingThisMonth = Booking::where('user_id', Auth::id())
            ->where('year', $y)->where('month', $m)
            ->whereIn('status_id', [Status::PENDING, Status::UNAVAILABLE]) 
            ->exists();

        $canBook = true;
        $cannotReason = null;

        if (! $stall->is_active) {
            $canBook = false;
            $cannotReason = 'ล็อกนี้ปิดใช้งาน';
        } elseif ($BookingThisMonth) {
            $canBook = false;
            $cannotReason = 'คุณมีการจองในเดือนนี้อยู่แล้ว (1 คน/เดือน จองได้ 1 ล็อก)';
        } elseif ($monthStatus && in_array($monthStatus->status_id, [Status::UNAVAILABLE, Status::PENDING, Status::CLOSED])) {
            $canBook = false;
            $cannotReason = 'เดือนนี้ล็อกนี้ไม่ว่าง/รออนุมัติ/ปิดให้จอง';
        }
        return view('vendor.stall_detail', compact('stall','y','m','startMonth','monthStatus','canBook','cannotReason'));
    }

    private function Ym(Request $request): array
    {
        $now = Carbon::now('Asia/Bangkok');

        $cy  = (int) $now->year;
        $cm = (int) $now->month;

        $y = $request->integer('year', $cy);
        $m = $request->integer('month', $cm);

        if ($y < $cy || ($y === $cy && $m < $cm)) {
            $y = $cy;
            $m = $cm;
        };

        $startMonth = ($y === $cy) ? $cm : 1;

        return [
            'y' => $y,'m' => $m,'cy' => $cy,'cm' => $cm,'startMonth' => $startMonth,'year' => $y,'month' => $m,
        ];
    }
}
