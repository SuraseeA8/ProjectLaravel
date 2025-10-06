<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Booking;


class Payment extends Model
{
    use HasFactory;

    protected $table = 'payment';          // ตารางนี้เป็นเอกพจน์
    protected $primaryKey = 'payment_id';
    public $timestamps = true;             // เพิ่ม created_at/updated_at แล้ว

    protected $fillable = [
        'booking_id',
        'acc_name',
        'bank',
        'payment_date',
        'amount',
        'slip_path',
    ];

    protected $casts = [
        'payment_date' => 'date',    // แปลงเป็น Carbon instance
        'amount'       => 'decimal:2',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }
}
