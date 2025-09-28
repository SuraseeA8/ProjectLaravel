<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
        'code',
        'size',
        'electricity',
        'water_fee',
        'price',
        'location',
        'stall_condition',
        
    ];

    // ความสัมพันธ์กับโซน
    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id');
    }

    // ความสัมพันธ์กับการจอง
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'stall_id');
    }

    // ความสัมพันธ์กับสถานะล็อก
    public function stallStatuses()
    {
        return $this->hasMany(Stall_status::class, 'stall_id');
    }
}
