<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stall extends Model
{
    use HasFactory;

    protected $table = 'stalls';
    protected $primaryKey = 'stall_id';

    protected $fillable = [
        'zone_id',
        'stall_code',
        'size',
        'electric_fee',
        'water_fee',
        'price',
        'location',
        'stall_condition',
        'is_active',       
    ];

    protected $casts = [
        'price'        => 'decimal:2',
        'electric_fee' => 'decimal:2',
        'water_fee'    => 'decimal:2',
        'is_active'    => 'boolean',
    ];

    /** ความสัมพันธ์กับโซน */
    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'zone_id', 'zone_id');
    }


    /** ความสัมพันธ์สถานะรายเดือน */
    public function statuses(): HasMany
    {
        return $this->hasMany(\App\Models\Stall_Status::class, 'stall_id', 'stall_id');
    }

    /** ความสัมพันธ์การจอง */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'stall_id', 'stall_id');
    }

    /** สถานะล่าสุดของเดือน/ปี ที่ระบุ */
    public function currentStatus(int $m, int $y)
    {
        return $this->statuses()
            ->where('month', $m)
            ->where('year',  $y)
            ->orderByDesc('stallstt_id')   // ล่าสุดสุดอยู่บน
            ->first();
    }

    /**  ใช้ซ้ำได้: เฉพาะล็อกที่เปิดใช้งาน */
    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }
}
