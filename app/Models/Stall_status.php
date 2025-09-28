<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Stall;
use App\Models\Status;
use App\Models\Booking;
use App\Models\User;

class Stall_status extends Model
{
    protected $table = 'stall_status';
    protected $primaryKey = 'stallstt_id';
    public $timestamps = false; // ถ้าไม่มี created_at / updated_at

    protected $fillable = [
        'stall_id',
        'status_id',
        'month',
        'year',
        'user_id',
        'booking_id',
    ];

    // ความสัมพันธ์กับ Stall
    public function stall()
    {
        return $this->belongsTo(Stall::class, 'stall_id');  
    }

    // ความสัมพันธ์กับ Status
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');    
    }

    // ความสัมพันธ์กับ Booking
    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    // ความสัมพันธ์กับ User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


}
