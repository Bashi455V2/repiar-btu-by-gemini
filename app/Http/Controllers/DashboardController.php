<?php

namespace App\Http\Controllers;

use App\Models\RepairRequest;
use App\Models\User;
use App\Models\Status; // เพิ่มการ import Status Model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // ข้อมูลนี้จะถูกแสดงเฉพาะ Admin ตาม Middleware ที่กำหนดใน routes/web.php
        $totalRequests = RepairRequest::count();

        // นับจำนวนตามสถานะโดยใช้ Relationship และชื่อสถานะจากตาราง statuses
        $pendingStatus = Status::where('name', 'รอดำเนินการ')->first();
        $inProgressStatus = Status::where('name', 'กำลังดำเนินการ')->first();
        $completedStatus = Status::where('name', 'ซ่อมเสร็จสิ้น')->first();

        $pendingRequests = $pendingStatus ? RepairRequest::where('status_id', $pendingStatus->id)->count() : 0;
        $inProgressRequests = $inProgressStatus ? RepairRequest::where('status_id', $inProgressStatus->id)->count() : 0;
        $completedRequests = $completedStatus ? RepairRequest::where('status_id', $completedStatus->id)->count() : 0;

        $users = User::orderBy('name')->paginate(10); // Admin สามารถดูผู้ใช้ทั้งหมด

        return view('dashboard', compact(
            'totalRequests',
            'pendingRequests',
            'inProgressRequests',
            'completedRequests',
            'users'
        ));
    }
}