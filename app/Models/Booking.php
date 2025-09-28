<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Stall;
use App\Models\Payment;
use App\Models\Status;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings';
    protected $primaryKey = 'booking_id';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'stall_id',
        'month',
        'year',
        'status_id',
    ];

    // ความสัมพันธ์กับ User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ความสัมพันธ์กับ Stall
    public function stall()
    {
        return $this->belongsTo(Stall::class, 'stall_id');
    }

    // ความสัมพันธ์กับ Payment
    public function payment()
    {
        return $this->hasOne(Payment::class, 'booking_id');
    }
    // ความสัมพันธ์กับ Status
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }
}
