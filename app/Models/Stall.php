<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\Zone;
use App\Models\Booking;
use App\Models\Stall_status;



class Stall extends Model
{
    protected $table = 'stalls';
    protected $primaryKey = 'stall_id';
    public $timestamps = true; // ถ้ามี created_at / updated_at

    protected $fillable = [
        'zone_id',
        'stall_code',
        'size',
        'electric_fee',
        'water_fee',
        'price',
        'location',
        'stall_condition',
        
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'electric_fee' => 'decimal:2',
        'water_fee' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // ความสัมพันธ์กับโซน
    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'zone_id', 'zone_id');
    }

    public function statuses(): HasMany
    {
        return $this->hasMany(Stall_Status::class, 'stall_id', 'stall_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'stall_id', 'stall_id');
    }

    public function currentStatus(int $m, int $y)
    {
        return $this->statuses()
            ->where('month', $m)->where('year', $y)
            ->latest('stallstt_id')->first();
    }

    
}
