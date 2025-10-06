<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Stall;

class Zone extends Model
{
    use HasFactory;

    protected $table = 'zones';
    protected $primaryKey = 'zone_id';
    public $timestamps = false;

    protected $fillable = ['zone_name', 'zone_code'];

    // ความสัมพันธ์กับ Stall
    public function stalls()
    {
        return $this->hasMany(Stall::class, 'zone_id', 'zone_id');
    }

    
}
