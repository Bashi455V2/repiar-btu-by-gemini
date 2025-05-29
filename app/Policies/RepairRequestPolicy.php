<?php

namespace App\Policies;

use App\Models\RepairRequest;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RepairRequestPolicy
{
    // (ใส่ method viewAny, view, create, update, delete, manageRequests ที่นี่
    //  ตามตัวอย่างที่ผมให้ไปในคำตอบก่อนหน้านี้เกี่ยวกับ RepairRequestPolicy.php)

    public function viewAny(User $user): bool
    {
        return true; // ทุกคนที่ล็อกอินสามารถเรียกดูรายการได้ (การ filter ทำใน controller)
    }

    public function view(User $user, RepairRequest $repairRequest): bool
    {
        if ($user->is_admin || $user->is_technician) {
            return true;
        }
        return $user->id === $repairRequest->user_id;
    }

    public function create(User $user): bool
    {
        return true; // ทุกคนที่ล็อกอินสามารถสร้างได้
    }

    public function update(User $user, RepairRequest $repairRequest): bool
    {
        if ($user->is_admin) {
            return true;
        }
        if ($user->is_technician) {
            // Technician อาจจะแก้ไขได้เฉพาะงานที่ assign ให้ตัวเอง หรือทั้งหมด
            // หรืออาจจะแก้ไขได้แค่บาง field (ซึ่ง Policy นี้จะควบคุมแค่ "สามารถเริ่ม" update ได้หรือไม่)
            return $repairRequest->assigned_to_user_id === $user->id || is_null($repairRequest->assigned_to_user_id);
        }
        // เจ้าของรายการ อาจจะแก้ไขได้เฉพาะตอนที่สถานะยังเป็น pending
        return $user->id === $repairRequest->user_id && optional($repairRequest->status)->name === 'รอดำเนินการ';
    }

    public function delete(User $user, RepairRequest $repairRequest): bool
    {
        if ($user->is_admin) {
            return true;
        }
        // ตัวอย่าง: เจ้าของลบได้ถ้ายังเป็น pending
        // return $user->id === $repairRequest->user_id && optional($repairRequest->status)->name === 'รอดำเนินการ';
        return false; // หรือถ้าไม่ให้ User ทั่วไปลบ
    }

    // ... (method อื่นๆ เช่น manageRequests ถ้าคุณจะใช้ผ่าน Policy)
}