<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Booking;
use Illuminate\Database\Eloquent\SoftDeletes;





class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'role_id',
        'users_fname',
        'users_lname',
        'email',
        'phone',
        'password',
        'is_active' => 'boolean',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'user_id', 'id');
    }

    public function updatedStallStatuses(): HasMany
    {
        return $this->hasMany(Stall_Status::class, 'user_id', 'id');
    }

    public function isAdmin(): bool
    {
        return (int) $this->role_id === 1; // ปรับให้ตรงค่าจริงในตาราง role
    }


    public function role()
    {
        return $this->belongsTo(Role::class, 'Role_id');
    }

    public function adminbookings()
    {
        return $this->hasMany(Booking::class, 'User_id');
    }

    public function shopDetail()
{
    return $this->hasOne(ShopDetail::class, 'User_id', 'id');
}

    public function stallStatuses()
    {
        return $this->hasMany(Stall_status::class, 'User_id');
    }

}
