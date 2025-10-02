<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Booking;
use App\Models\Stall_status;
use App\Models\Payment;


class Status extends Model
{
    use HasFactory;

    protected $table = 'status';
    protected $primaryKey = 'status_id';
    public $timestamps = false;

    protected $fillable = ['status_name','status_code'];

    // ค่ามาตรฐานไว้ใช้ในโค้ด
    public const AVAILABLE   = 1;
    public const UNAVAILABLE = 2;
    public const PENDING     = 3;
    public const CLOSED      = 4;

    public function stallStatuses(): HasMany
    {
        return $this->hasMany(Stall_Status::class, 'status_id', 'status_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'status_id', 'status_id');
    }


}
