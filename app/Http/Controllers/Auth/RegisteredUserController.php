<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ShopDetail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'users_fname' => ['required', 'string', 'max:100'],
            'users_lname' => ['required', 'string', 'max:100'],
            'email'      => ['required', 'string', 'email', 'max:100', 'unique:users'],
            'phone'      => ['required', 'string', 'max:10'],
            'shop_name'   => ['required', 'string', 'max:45'],     // shop_detail.shop_name (varchar 45) :contentReference[oaicite:2]{index=2}
            'description' => ['nullable', 'string', 'max:255'], // shop_detail.Description (varchar 255) :contentReference[oaicite:2]{index=3}
            'password'   => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'role_id'     => 2, // 1=admin, 2=vendor
            'users_fname' => $request->users_fname,
            'users_lname' => $request->users_lname,
            'email'       => $request->email,
            'phone'       => $request->phone,
            'password'    => Hash::make($request->password),
        ]);

        ShopDetail::create([
            'user_id'     => $user->id,           // FK -> users.id :contentReference[oaicite:4]{index=4}
            'shop_name'   => $request->shop_name,
            'description' => $request->description ?? '',
        ]);

        event(new Registered($user));

        Auth::login($user);

        if ($user->role === 'admin') {
            return redirect()->route('admin.stalls');
        } else {
            return redirect()->route('vendor.home');
        }


    }
}
