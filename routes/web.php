<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RepairRequestController;
use Illuminate\Support\Facades\Route;
use App\Models\RepairRequest;
use App\Models\User;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\HasRole; // <--- เพิ่มบรรทัดนี้เข้ามา (สำคัญมาก!)

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Route สำหรับ Dashboard
Route::get('/dashboard', function () {
    $totalRequests = RepairRequest::count();
    $pendingRequests = RepairRequest::where('status', 'pending')->count();
    $inProgressRequests = RepairRequest::where('status', 'in_progress')->count();
    $completedRequests = RepairRequest::where('status', 'completed')->count();

    $users = collect();
    if (Auth::check() && (Auth::user()->is_admin || Auth::user()->is_technician)) {
        $users = User::paginate(10);
    }

    return view('dashboard', compact('totalRequests', 'pendingRequests', 'inProgressRequests', 'completedRequests', 'users'));
})->middleware(['auth', 'verified'])->name('dashboard');

// --- รวม Group Route ที่ต้องมีการล็อกอินทั้งหมดไว้ในบล็อกเดียว ---
Route::middleware('auth')->group(function () {
    // Routes สำหรับ Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ******** ย้าย Route /repair_requests/manage มาไว้ข้างบน Route::resource ********
    // Route สำหรับ Admin/Technician เพื่อจัดการรายการแจ้งซ่อมทั้งหมด
    Route::get('/repair_requests/manage', [RepairRequestController::class, 'manage'])
        ->middleware(HasRole::class . ':admin,technician') // ใช้ HasRole::class โดยตรง
        ->name('repair_requests.manage');

    // Route สำหรับการอัปเดตสถานะ/มอบหมายงาน (ใช้ PUT/PATCH)
    Route::put('/repair_requests/{repairRequest}/update_status_assign', [RepairRequestController::class, 'updateStatusAndAssign'])
        ->middleware(HasRole::class . ':admin,technician') // ใช้ HasRole::class โดยตรง
        ->name('repair_requests.update_status_assign');

    // Route สำหรับผู้ใช้งานทั่วไป (ทุกคนที่ล็อกอินเข้าสู่ระบบได้)
    // สามารถสร้าง/ดูรายการแจ้งซ่อมของตัวเองได้
    Route::resource('repair_requests', RepairRequestController::class); // <--- บรรทัดนี้ยังอยู่ แต่ย้าย manage ไปข้างบนแล้ว

    // Routes สำหรับการจัดการผู้ใช้ (Admin Only)
    Route::middleware(HasRole::class . ':admin')->group(function () { // ใช้ HasRole::class โดยตรง
        Route::resource('users', UserController::class);
    });
});

require __DIR__.'/auth.php';