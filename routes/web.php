<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Public Routes (ไม่ต้องล็อกอิน)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('index'); // หน้าแรก
})->name('index');

/*
|--------------------------------------------------------------------------
| Dashboard (เฉพาะคนที่ล็อกอิน)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Profile Routes (auth only)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Vendor Routes
|--------------------------------------------------------------------------
*/
Route::prefix('vendor')->middleware('auth')->group(function () {
    // หน้า Home ของ Vendor
    Route::get('home', [VendorController::class, 'showHome'])->name('vendor.home'); // หน้าแรกของผู้ขายหลังจากเข้าสู่ระบบ

    // โปรไฟล์
    Route::get('profile', [VendorController::class, 'showProfile'])->name('vendor.profile'); // หน้าแสดงโปรไฟล์ผู้ขาย 
    Route::post('profile', [VendorController::class, 'updateProfile']); // อัปเดตโปรไฟล์ผู้ขาย

    // ล็อกตลาด
    Route::get('stalls', [VendorController::class, 'stallList'])->name('vendor.stalls'); // หน้าแสดงล็อกที่สามารถจองได้
    Route::get('stalls/{id}', [VendorController::class, 'stallDetail'])->name('vendor.stall.detail'); // รายละเอียดล็อก
    Route::post('stalls/{id}/book', [VendorController::class, 'bookStall'])->name('vendor.stall.book'); // จองล็อก

    // สถานะการจอง
    Route::get('booking/status', [VendorController::class, 'bookingStatus'])->name('vendor.booking.status'); // หน้าแสดงสถานะการจองของผู้ขาย
    Route::post('booking/{id}/cancel', [VendorController::class, 'cancelBooking'])->name('vendor.booking.cancel'); // ยกเลิกการจอง

    // อัปโหลดสลิป
    Route::get('booking/{id}/upload-slip', [VendorController::class, 'uploadSlipForm'])->name('vendor.booking.slip'); // ฟอร์มอัปโหลดสลิป
    Route::post('booking/{id}/upload-slip', [VendorController::class, 'storeSlip']); // บันทึกสลิป

    // ข่าวสาร/กิจกรรม
    Route::get('events', [VendorController::class, 'eventBoard'])->name('vendor.events'); // หน้าแสดงประกาศต่างๆ

    // ออกจากระบบ (ใช้ logout ของ Laravel ก็ได้)
    Route::post('logout', [VendorController::class, 'logout'])->name('vendor.logout'); // ออกจากระบบ
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth'])->group(function () {
    // การจัดการล็อก
    Route::get('stalls', [AdminController::class, 'stallManagement'])->name('admin.stalls');
    Route::post('stalls/{id}/toggle', [AdminController::class, 'toggleStall']);
    Route::post('stalls/{id}/cancel', [AdminController::class, 'cancelStall']);

    // การจัดการผู้ใช้
    Route::get('users', [AdminController::class, 'userManagement'])->name('admin.users');
    Route::delete('users/{id}', [AdminController::class, 'deleteUser']);

    // การจัดการกิจกรรม
    Route::get('events', [AdminController::class, 'eventManagement'])->name('admin.events');
    Route::post('events/create', [AdminController::class, 'createEvent']);
    Route::post('events/{id}/edit', [AdminController::class, 'editEvent']);

    // ตรวจสอบสลิป
    Route::get('payments', [AdminController::class, 'paymentCheck'])->name('admin.payments');
    Route::post('payments/{id}/approve', [AdminController::class, 'approvePayment']);
    Route::post('payments/{id}/reject', [AdminController::class, 'rejectPayment']);

    // รายงาน
    Route::get('reports', [AdminController::class, 'reportDashboard'])->name('admin.reports');
});

/*
|--------------------------------------------------------------------------
| Auth Routes (Breeze/Fortify)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
