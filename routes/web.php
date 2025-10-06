<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Auth;




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
    Route::get('stalls', [AdminController::class, 'stallManagement'])->name('admin.stalls');
    Route::post('stalls/{id}/toggle', [AdminController::class, 'toggleStall'])->whereNumber('id');
    Route::post('stalls/{id}/cancel', [AdminController::class, 'cancelStall'])->whereNumber('id');

    // การจัดการผู้ใช้
    Route::get('users', [AdminController::class, 'userManagement'])->name('admin.users');
    Route::delete('users/{id}', [AdminController::class, 'deleteUser'])->whereNumber('id');

    // การจัดการกิจกรรม
    Route::get('events', [AdminController::class, 'eventManagement'])->name('admin.events');
    Route::post('events/create', [AdminController::class, 'createEvent']);
    Route::post('events/{id}/edit', [AdminController::class, 'editEvent'])->whereNumber('id');

    // ตรวจสอบสลิป
    Route::get('payments', [AdminController::class, 'paymentCheck'])->name('admin.payments');
    Route::post('payments/{id}/approve', [AdminController::class, 'approvePayment'])->whereNumber('id');
    Route::post('payments/{id}/reject',  [AdminController::class, 'rejectPayment'])->whereNumber('id');

    // รายงาน
    Route::get('reports', [AdminController::class, 'reportDashboard'])->name('admin.reports');
});

/*
|--------------------------------------------------------------------------
| Auth Routes (Breeze/Fortify)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
