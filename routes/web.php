<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RepairRequestController;
use Illuminate\Support\Facades\Route;
// use App\Models\RepairRequest; // <--- **เอา Model ออกจากการเรียกโดยตรงใน Route Closure**
// use App\Models\User;        // <--- **เอา Model ออกจากการเรียกโดยตรงใน Route Closure**
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController; // <--- **เพิ่ม Use Statement นี้**
// use Illuminate\Support\Facades\Auth; // <--- ไม่จำเป็นใน Route Closure อีกต่อไป
use App\Http\Middleware\HasRole;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('welcome');
// Route สำหรับหน้าแรก (Welcome Page)

// Route สำหรับ Dashboard (แก้ไขแล้ว)
Route::get('/dashboard', [DashboardController::class, 'index']) // <--- **เปลี่ยนให้เรียก Controller**
    ->middleware(['auth', 'verified', HasRole::class . ':admin']) // <--- **เพิ่ม HasRole middleware**
    ->name('dashboard');

// --- รวม Group Route ที่ต้องมีการล็อกอินทั้งหมดไว้ในบล็อกเดียว ---
Route::middleware('auth')->group(function () { // คุณอาจจะเพิ่ม 'verified' ที่นี่ถ้าต้องการให้ทุกหน้าหลัง login ต้อง verify
    // Routes สำหรับ Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ******** ย้าย Route /repair_requests/manage มาไว้ข้างบน Route::resource ********
    // Route สำหรับ Admin/Technician เพื่อจัดการรายการแจ้งซ่อมทั้งหมด
    Route::get('/repair_requests/manage', [RepairRequestController::class, 'manage'])
        ->middleware(HasRole::class . ':admin,technician')
        ->name('repair_requests.manage');

    // Route สำหรับการอัปเดตสถานะ/มอบหมายงาน (ใช้ PUT/PATCH)
    Route::put('/repair_requests/{repairRequest}/update_status_assign', [RepairRequestController::class, 'updateStatusAndAssign'])
        ->middleware(HasRole::class . ':admin,technician')
        ->name('repair_requests.update_status_assign');

    // Route สำหรับผู้ใช้งานทั่วไป (ทุกคนที่ล็อกอินเข้าสู่ระบบได้)
    Route::resource('repair_requests', RepairRequestController::class);

    // Routes สำหรับการจัดการผู้ใช้ (Admin Only)
    Route::middleware(HasRole::class . ':admin')->group(function () {
        Route::resource('users', UserController::class);
    });
});

require __DIR__.'/auth.php';