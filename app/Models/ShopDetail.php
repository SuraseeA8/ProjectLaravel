<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ShopDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'shop_detail';
    protected $primaryKey = 'shop_detail_id';
    public $timestamps = false;

    protected $fillable = [
        'shop_name',
        'description',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
