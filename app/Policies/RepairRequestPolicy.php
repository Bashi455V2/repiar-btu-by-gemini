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
        if ($user->is_admin) {
            return true; // แอดมินสามารถดูได้ทุกรายการ
        }

        if ($user->is_technician) {
            return true; // **แก้ไขตรงนี้**: ช่างเทคนิคทุกคนสามารถดูรายการซ่อมได้ทั้งหมด (รวมถึงของช่างคนอื่น)
        }

        // ผู้ใช้ทั่วไปสามารถดูได้เฉพาะรายการที่ตัวเองแจ้งเท่านั้น
        return $user->id === $repairRequest->user_id;
    }

    // (ส่วนนี้เหมือนเดิม - อนุญาตให้ update เฉพาะงานที่ถูกมอบหมายให้ตัวเอง หรือ Admin)
    public function create(User $user): bool
    {
        return true; // ทุกคนที่ล็อกอินสามารถสร้างได้
    }

    public function update(User $user, RepairRequest $repairRequest): bool
    {
        if ($user->is_admin) {
            return true; // แอดมินสามารถอัปเดตได้ทุกรายการ
        }
        if ($user->is_technician) {
            // ช่างเทคนิคสามารถแก้ไขได้เฉพาะงานที่ถูกมอบหมายให้ตัวเองเท่านั้น
            // หรือถ้าเป็นงานที่ยังไม่ได้มอบหมายก็อาจจะยังแก้ไขได้ (เช่น เปลี่ยนสถานะเบื้องต้น)
            // แต่สำหรับงานที่ "ช่างคนอื่น" รับไปแล้ว จะไม่สามารถแก้ไขได้ตาม Policy นี้
            return $repairRequest->assigned_to_user_id === $user->id || is_null($repairRequest->assigned_to_user_id);
        }
        // เจ้าของรายการ อาจจะแก้ไขได้เฉพาะตอนที่สถานะยังเป็น "รอดำเนินการ"
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

    public function claim(User $user, RepairRequest $repairRequest): bool
    {
        // อนุญาตให้ช่างรับงานได้ก็ต่อเมื่อยังไม่มีใครรับงานนั้น
        return $user->is_technician && is_null($repairRequest->assigned_to_user_id);
    }

    // ... (method อื่นๆ เช่น manageRequests ถ้าคุณจะใช้ผ่าน Policy)
}