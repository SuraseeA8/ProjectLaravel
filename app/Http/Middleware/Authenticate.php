<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo($request): ?string
    {
        // ถ้า request ไม่ใช่ JSON → redirect ไปหน้า login
        if (! $request->expectsJson()) {
            return route('login');
        }

        return null;
    }
}
