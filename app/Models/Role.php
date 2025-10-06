<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    
    protected $table = 'role';
    protected $primaryKey = 'Role_id';
    public $timestamps = false;

    protected $fillable = ['Role_name'];

    public function users()
    {
        return $this->hasMany(User::class, 'Role_id');
    }
}