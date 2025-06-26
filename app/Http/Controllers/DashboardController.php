<?php

namespace App\Http\Controllers;

use App\Models\RepairRequest;
use App\Models\User;
use App\Models\Status; // เพิ่มการ import Status Model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
   // ใน App\Http\Controllers\DashboardController.php
public function index()
{
    $user = Auth::user();
    if ($user && method_exists($user, 'is_admin') && $user->is_admin) {
        return redirect()->route('admin.dashboard'); // ไปที่ /admin/dashboard
    } elseif ($user && method_exists($user, 'is_technician') && $user->is_technician) {
        return redirect()->route('repair_requests.index');
    }
    // สำหรับ User ทั่วไป
    return redirect()->route('repair_requests.index');
}
}