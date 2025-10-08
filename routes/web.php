<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Vendor\ProfileController;
use App\Http\Controllers\Vendor\VendorController;
use App\Http\Controllers\Vendor\HomeController;
use App\Http\Controllers\Vendor\StallController;
use App\Http\Controllers\Vendor\BookingController;
use App\Http\Controllers\Vendor\CheckoutController;


use App\Http\Controllers\AdminController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Auth; 


use App\Http\Controllers\StallManageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminProfileController;
/*
|--------------------------------------------------------------------------
| Public Routes (ไม่ต้องล็อกอิน)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (Auth::check()) {
        $roleId = Auth::user()->role_id ?? null;
        return $roleId == 1
            ? redirect()->route('admin.home')
            : redirect()->route('vendor.home');
    }
    return view('index'); // ยังไม่ล็อกอิน → หน้า landing
})->name('index');

Route::get('/board', [EventController::class, 'board'])->name('board');

/*
|--------------------------------------------------------------------------
| Profile Routes (auth only)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',[ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Vendor Routes (ทั้งหมดต้องล็อกอินก่อนดู/จองได้)
|--------------------------------------------------------------------------
*/
Route::prefix('vendor')->middleware(['auth'])->group(function () {
    Route::get('home', [HomeController::class, 'showHome'])->name('vendor.home');
    
    Route::get('profile', [ProfileController::class, 'showProfile'])->name('vendor.edit');
    Route::post('profile',[ProfileController::class, 'updateProfile'])->name('vendor.profile.update');
    Route::post('profile/password', [ProfileController::class, 'changePassword'])->name('vendor.profile.password');
    Route::post('profile/shop', [ProfileController::class, 'updateShop'])->name('vendor.shop.update');

    Route::get('stalls',[StallController::class, 'stallList'])->name('vendor.stalls');
    Route::get('stalls/{stall}', [StallController::class, 'stallDetail'])->name('vendor.stall.detail');
    Route::post('stalls/{stall}/book', [BookingController::class, 'bookStall'])->name('vendor.stall.book'); // จองล็อก

    Route::get('booking/status', [BookingController::class, 'bookingStatus'])->name('vendor.booking.status');
    Route::post('booking/{booking}/cancel', [BookingController::class, 'cancelBooking'])->name('vendor.booking.cancel');
    
    Route::get('stalls/{stall}/checkout',  [CheckoutController::class, 'checkoutForm'])->name('vendor.stall.checkout');

    Route::post('stalls/{stall}/checkout', [CheckoutController::class, 'checkoutSubmit'])->name('vendor.stall.checkout.submit');
        
    Route::get('events', [VendorController::class, 'eventBoard'])->name('vendor.events');
});

/*
|--------------------------------------------------------------------------
| Admin Routes (ต้องล็อกอิน + สิทธิ์ admin)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth','can:admin'])->group(function () {
    // การจัดการล็อก
    
    Route::get('home', [AdminController::class, 'showHome'])->name('admin.home');
    Route::get('stalls', [StallManageController::class, 'index'])->name('admin.stalls.index');


    Route::get('stalls/toggle/{stall_id}/{month}/{year}', [StallManageController::class, 'toggleStatus'])->name('admin.stalls.toggle');

    Route::get('users', [UserController::class, 'index'])->name('admin.users.index'); 
    Route::post('/delete/{id}', [UserController::class, 'destroy'])->name('admin.users.delete');

    Route::get('bookings', [StallManageController::class, 'manage'])->name('admin.booking.manage');
    Route::get('bookings/approve/{id}', [StallManageController::class, 'approve'])->name('admin.booking.approve');
    Route::get('bookings/cancel/{id}', [StallManageController::class, 'cancel'])->name('admin.booking.cancel');


    Route::get('profile', [AdminProfileController::class, 'index'])->name('admin.profile');
    Route::put('profile/update', [AdminProfileController::class, 'update'])->name('admin.profile.update');

    Route::put('password/update', [AdminProfileController::class, 'passwordupdate'])->name('admin.profile.password');

    // การจัดการผู้ใช้
    

    // Event management
    Route::get('events', [EventController::class, 'adminIndex'])->name('admin.events.index');
    Route::get('events/create', [EventController::class, 'create'])->name('admin.events.create');
    Route::post('events', [EventController::class, 'store'])->name('admin.events.store');
    Route::get('events/{event}/edit', [EventController::class, 'edit'])->name('admin.events.edit');
    Route::put('events/{event}', [EventController::class, 'update'])->name('admin.events.update');
    Route::delete('events/{event}', [EventController::class, 'destroy'])->name('admin.events.destroy');

    // Reports
// Reports
    Route::get('reports/bookings', [ReportController::class, 'bookingReport'])->name('admin.reports.bookings');

    // ตรวจสอบสลิป
    
});



/*
|--------------------------------------------------------------------------
| Auth Routes (Breeze/Fortify)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
