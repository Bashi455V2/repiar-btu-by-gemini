<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RepairRequestController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController; // <--- **เพิ่ม Use Statement นี้**
// use Illuminate\Support\Facades\Auth; // <--- ไม่จำเป็นใน Route Closure อีกต่อไป
use App\Http\Middleware\HasRole;
use App\Http\Controllers\Admin\StatusController;
use App\Http\Controllers\Admin\LocationController; // <--- **เพิ่ม Use Statement สำหรับ LocationController**
use App\Http\Controllers\Admin\CategoryController; // <--- **เพิ่ม Use Statement สำหรับ CategoryController**
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

   
    // ...
    Route::middleware(HasRole::class . ':admin')
         ->prefix('admin')
         ->name('admin.')
         ->group(function () {
        Route::resource('users', \App\Http\Controllers\UserController::class)->except(['show']);
        Route::resource('locations', \App\Http\Controllers\Admin\LocationController::class); // ตัวอย่างถ้า LocationController อยู่ใน Admin namespace
        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class); // ตัวอย่าง
        Route::resource('statuses', StatusController::class); // <--- **ตรวจสอบ Controller และ Namespace ที่นี่**
    });

});

require __DIR__.'/auth.php';