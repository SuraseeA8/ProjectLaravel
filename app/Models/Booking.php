<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;
use App\Models\Stall;
use App\Models\Payment;
use App\Models\Status;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings';
    protected $primaryKey = 'booking_id';
    public $timestamps = true; // มี created_at / updated_at แล้ว

    protected $fillable = [
        'user_id','stall_id','year','month','status_id'
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
    ];

    // ความสัมพันธ์
    public function stall(): BelongsTo
    {
        return $this->belongsTo(Stall::class, 'stall_id', 'stall_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status_id', 'status_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'booking_id', 'booking_id');
    }

    // Scopes
    public function scopeYm(Builder $q, int $year, int $month): Builder
    {
        return $q->where('year',$year)->where('month',$month);
    }

    public function scopePending(Builder $q): Builder
    {
        return $q->where('status_id', Status::PENDING);
    }

    public function scopeApproved(Builder $q): Builder
    {
        return $q->where('status_id', Status::UNAVAILABLE); // อนุมัติแล้ว = ไม่ว่าง
    }

    // helper: แสดงช่วงแบบ YYYY-MM
    public function getPeriodAttribute(): string
    {
        return sprintf('%04d-%02d', $this->year, $this->month);
    }


    public function adminuser()
    {
        
        return $this->belongsTo(User::class, 'user_id', 'User_id');
    }

    public function adminstall()
    {
        // แก้ให้ตรงกับคีย์ในฐานข้อมูล (stall_id)
        return $this->belongsTo(Stall::class, 'stall_id', 'stall_id');
    }


    public function payment()
    {
        return $this->hasOne(Payment::class, 'booking_id', 'booking_id');
    }

    public function stallStatus()
    {
        return $this->hasOne(Stall_status::class, 'stall_id', 'Stall_id')
                    ->whereColumn('month', 'month')
                    ->whereColumn('year', 'year')
                    ->whereColumn('User_id', 'User_id');
    }


}
