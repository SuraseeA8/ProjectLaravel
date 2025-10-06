<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'event';
    protected $primaryKey = 'event_id';
    public $timestamps = false;

    protected $fillable = [
        'title',
        'detail',
        'start_date',
        'end_date',
        'img_path'
    ];
}