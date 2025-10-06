<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Auth; 


use App\Http\Controllers\StallManageController;
use App\Http\Controllers\UserController;
/*
|--------------------------------------------------------------------------
| Public Routes (ไม่ต้องล็อกอิน)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (Auth::check()) {
        $roleId = Auth::user()->role_id ?? null;
        return $roleId == 1
            ? redirect()->route('admin.stalls')
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
    // Home / Profile
    Route::get('home', [VendorController::class, 'showHome'])->name('vendor.home');
    
    Route::get('profile', [VendorController::class, 'showProfile'])->name('vendor.profile');
    Route::post('profile',[VendorController::class, 'updateProfile']);

    // ล็อกตลาด (ดูได้เมื่อล็อกอินเท่านั้น)
    Route::get('stalls',[VendorController::class, 'stallList'])->name('vendor.stalls');
    Route::get('stalls/{stall}', [VendorController::class, 'stallDetail'])->name('vendor.stall.detail');
    Route::post('stalls/{stall}/book', [VendorController::class, 'bookStall'])->name('vendor.stall.book'); // จองล็อก

    // สถานะการจอง
    Route::get('booking/status', [VendorController::class, 'bookingStatus'])->name('vendor.booking.status');
    Route::post('booking/{booking}/cancel', [VendorController::class, 'cancelBooking'])->name('vendor.booking.cancel');
    
    Route::get('stalls/{stall}/checkout',  [VendorController::class, 'checkoutForm'])
        ->name('vendor.stall.checkout');
    // ส่งฟอร์ม: สร้าง booking + payment + อัพเดต stall_status ในคราวเดียว
    Route::post('stalls/{stall}/checkout', [VendorController::class, 'checkoutSubmit'])
        ->name('vendor.stall.checkout.submit');
        
    // ข่าวสาร/กิจกรรม
    Route::get('events', [VendorController::class, 'eventBoard'])->name('vendor.events');
    // ❌ ไม่ต้องประกาศ logout เอง ถ้าใช้ Breeze/Fortify จะมี POST /logout จาก auth.php อยู่แล้ว
});

/*
|--------------------------------------------------------------------------
| Admin Routes (ต้องล็อกอิน + สิทธิ์ admin)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth','can:admin'])->group(function () {
    // การจัดการล็อก
    // Route::get('stalls', [AdminController::class, 'stalls'])->name('admin.stalls');
    
    
    Route::get('/admin/stalls', [StallManageController::class, 'index'])->name('admin.stalls.index');


    Route::get('/admin/stalls/toggle/{stall_id}/{month}/{year}', [StallManageController::class, 'toggleStatus'])->name('admin.stalls.toggle');

    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index'); 
    Route::post('/delete/{id}', [UserController::class, 'destroy'])->name('admin.users.delete');

    Route::get('/admin/bookings', [StallManageController::class, 'manage'])->name('admin.booking.manage');
    Route::get('/admin/bookings/approve/{id}', [StallManageController::class, 'approve'])->name('admin.booking.approve');
    Route::get('/admin/bookings/cancel/{id}', [StallManageController::class, 'cancel'])->name('admin.booking.cancel');

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
