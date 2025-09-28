<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Booking;
use App\Models\Stall_status;
use App\Models\Payment;


class Status extends Model
{
    protected $table = 'status';
    protected $primaryKey = 'status_id';
    public $timestamps = false; // ถ้าไม่มี created_at / updated_at

    protected $fillable = [
        'status_name',
    ];

    // ความสัมพันธ์กับการจอง
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'status_id');
    }

    // ความสัมพันธ์กับสถานะล็อก
    public function stallStatuses()
    {
        return $this->hasMany(Stall_status::class, 'status_id');
    }

    // ความสัมพันธ์กับการชำระเงิน
    public function payments()
    {
        return $this->hasMany(Payment::class, 'status');
    }


}
