<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // ✅ สร้าง Gate admin
        Gate::define('admin', function ($user) {
            return $user->role_id == 1; // กำหนดว่า role_id = 1 คือ admin
        });

        Carbon::setLocale('th');


    }

}