<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\HasRole;

// Controllers
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RepairRequestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\StatusController;

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
// Controller จะทำหน้าที่ตรวจสอบ Role และ Redirect ไปยังหน้าที่เหมาะสม
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// --- Group Route ทั้งหมดที่ต้องมีการล็อกอิน ---
Route::middleware('auth')->group(function () {

    // Routes สำหรับ Profile (ใช้ร่วมกันทุก Role)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- Technician Routes ---
    Route::middleware(HasRole::class . ':technician')->prefix('technician')->name('technician.')->group(function () {
        Route::get('/tasks', [RepairRequestController::class, 'technicianTasks'])->name('tasks.index');
        Route::get('/queue', [RepairRequestController::class, 'technicianQueue'])->name('queue.index');
    });

    // --- Admin Group ---
    Route::middleware(HasRole::class . ':admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/manage', [RepairRequestController::class, 'manage'])->name('manage');
        Route::get('/repair_requests/{repairRequest}/edit', [RepairRequestController::class, 'edit'])->name('repair_requests.edit');
        Route::put('/repair_requests/{repairRequest}', [RepairRequestController::class, 'update'])->name('repair_requests.update');
        
        // Master Data Management
        Route::resource('users', UserController::class)->except(['show']);
        Route::resource('locations', LocationController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('statuses', StatusController::class);
    });
        
    // --- Shared Actions & General User Routes ---
    Route::put('/repair_requests/{repairRequest}/update_status_assign', [RepairRequestController::class, 'updateStatusAndAssign'])->middleware(HasRole::class . ':admin,technician')->name('repair_requests.update_status_assign');
    Route::patch('/repair_requests/{repairRequest}/claim', [RepairRequestController::class, 'claim'])->middleware(HasRole::class . ':technician')->name('repair_requests.claim');

    // Route::resource สำหรับ Repair Requests จะจัดการส่วนที่เหลือ (create, store, show, destroy)
    Route::resource('repair_requests', RepairRequestController::class);

});

require __DIR__.'/auth.php';
