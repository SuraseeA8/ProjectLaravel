<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Stall;
use App\Models\Status;
use App\Models\Booking;
use App\Models\User;
use App\Models\Payment;


class Stall_status extends Model
{
    use HasFactory;

    protected $table = 'stall_status';
    protected $primaryKey = 'stall_status_id';

    // ตารางนี้มีเฉพาะ updated_at (ตามแพตช์) → ปิด created_at
    public $timestamps = false;
    const CREATED_AT = null;
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'stall_id','year','month','status_id','reason','updated_at','booking_id','user_id'
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'updated_at' => 'datetime',
    ];

    // ความสัมพันธ์
    public function stall(): BelongsTo
    {
        return $this->belongsTo(Stall::class, 'stall_id', 'stall_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status_id', 'status_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // ---- Scopes ----
    public function scopeYm(Builder $q, int $year, int $month): Builder
    {
        return $q->where('year',$year)->where('month',$month);
    }

    public function scopeAvailable(Builder $q): Builder
    {
        return $q->where('status_id', Status::AVAILABLE);
    }

    public function scopeBusy(Builder $q): Builder
    {
        // ไม่ว่าง/รออนุมัติ/ปิด
        return $q->whereIn('status_id', [Status::UNAVAILABLE, Status::PENDING, Status::CLOSED]);
    }
}