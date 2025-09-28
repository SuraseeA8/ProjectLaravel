<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Booking;


class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';
    protected $primaryKey = 'payment_id';
    public $timestamps = true;

    protected $fillable = [
        'booking_id',
        'amount',
        'slip_image',
        'status',
    ];

    // ความสัมพันธ์กับ Booking
    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }


}
