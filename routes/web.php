<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RepairRequestController;
use Illuminate\Support\Facades\Route;

// --- Controllers ---
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\AdminDashboardController; // แนะนำให้สร้าง Controller แยกสำหรับ Admin Dashboard เพื่อความชัดเจน
use App\Http\Controllers\Admin\StatusController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\CategoryController;

// --- Middleware ---
use App\Http\Middleware\HasRole;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- หน้าแรกสำหรับ Guest ---
Route::get('/', function () {
    return view('welcome');
})->name('welcome');


// --- Route หลักหลังการ Login (สำหรับ RouteServiceProvider::HOME) ---
// Route นี้ควรเป็นจุดเริ่มต้นกลางสำหรับผู้ใช้ทุกคนหลัง Login
// Controller จะทำหน้าที่ตรวจสอบ Role และ Redirect ไปยังหน้าที่เหมาะสม
// จึงไม่ควรมี Middleware HasRole:admin ที่นี่
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


// --- Group Route ทั้งหมดที่ต้องมีการล็อกอิน ---
Route::middleware('auth')->group(function () {

    // Routes สำหรับ Profile (ใช้ร่วมกันทุก Role)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Route สำหรับการอัปเดตสถานะ/มอบหมายงาน (ใช้โดย Admin/Technician)
    Route::put('/repair_requests/{repairRequest}/update_status_assign', [RepairRequestController::class, 'updateStatusAndAssign'])
        ->middleware(HasRole::class . ':admin,technician')
        ->name('repair_requests.update_status_assign');

    // Resource Controller สำหรับ Repair Requests
    // จะจัดการ Route: index, create, store, show, edit, update, destroy
    // index จะถูกใช้โดย User ทั่วไป และ Technician
    Route::resource('repair_requests', RepairRequestController::class);


    // --- Admin Group: Routes ทั้งหมดสำหรับ Admin เท่านั้น ---
    Route::middleware(HasRole::class . ':admin')
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // ใช้ Full Namespace Path หรือ use statement ที่ถูกต้อง
        Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/manage', [App\Http\Controllers\RepairRequestController::class, 'manage'])->name('manage'); // manage method ยังอยู่ที่ RepairRequestController
         // VVVVVV เพิ่ม 2 บรรทัดนี้เข้าไปใน Admin Group VVVVVV
            Route::get('/repair_requests/{repairRequest}/edit', [App\Http\Controllers\RepairRequestController::class, 'edit'])->name('repair_requests.edit');
            Route::put('/repair_requests/{repairRequest}', [App\Http\Controllers\RepairRequestController::class, 'update'])->name('repair_requests.update');
            // ^^^^^^ เพิ่ม 2 บรรทัดนี้เข้าไปใน Admin Group ^^^^^^

        Route::resource('users', \App\Http\Controllers\UserController::class)->except(['show']); // UserController อาจจะอยู่ที่ root
        Route::resource('locations', LocationController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('statuses', StatusController::class);
    });
});

require __DIR__.'/auth.php';
