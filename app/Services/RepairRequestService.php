<?php

namespace App\Services;

use App\Models\RepairRequest;
use App\Models\Status;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity; // *** เพิ่มบรรทัดนี้เข้ามา ***

class RepairRequestService
{
    /**
     * สร้างรายการแจ้งซ่อมใหม่
     */
    public function createRepairRequest(array $validatedData, User $creatingUser, ?UploadedFile $imageFile = null): RepairRequest
    {
        $imagePath = null;
        if ($imageFile) {
            $imagePath = $imageFile->store('repair_images', 'public');
        }

        $defaultStatus = Status::where('name', 'รอดำเนินการ')->firstOrFail();

        $createData = [
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'location_id' => $validatedData['location_id'],
            'category_id' => $validatedData['category_id'],
            'status_id' => $defaultStatus->id,
            'requester_phone' => $validatedData['requester_phone'] ?? ($creatingUser->phone_number ?? null),
        ];

        if ($imagePath) {
            $createData['image_path'] = $imagePath;
        }

        $repairRequest = $creatingUser->repairRequests()->create($createData);

        activity()
            ->performedOn($repairRequest)
            ->causedBy($creatingUser)
            ->log('สร้างรายการแจ้งซ่อม: ' . Str::limit($repairRequest->title, 50));

        return $repairRequest;
    }

    /**
     * อัปเดตรายการแจ้งซ่อม (จากหน้า Edit หลัก)
     */
    public function updateRepairRequest(
        RepairRequest $repairRequest,
        array $validatedData,
        User $updatingUser,
        ?UploadedFile $imageFile = null,
        bool $clearImage = false,
        ?UploadedFile $afterRepairImageFile = null,
        bool $clearAfterImage = false
    ): array { // <-- แก้ไขให้ return array

        $statusChanged = isset($validatedData['status_id']) && $validatedData['status_id'] != $repairRequest->status_id;

        $dataToUpdate = [
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'location_id' => $validatedData['location_id'],
            'category_id' => $validatedData['category_id'],
            'requester_phone' => $validatedData['requester_phone'] ?? $repairRequest->requester_phone,
        ];

        // จัดการรูปภาพประกอบปัญหา (รูปแรก)
        if ($imageFile) {
            if ($repairRequest->image_path) { Storage::disk('public')->delete($repairRequest->image_path); }
            $dataToUpdate['image_path'] = $imageFile->store('repair_images', 'public');
        } elseif ($clearImage && $repairRequest->image_path) {
            Storage::disk('public')->delete($repairRequest->image_path);
            $dataToUpdate['image_path'] = null;
        }

        // จัดการรูปภาพหลังการซ่อม
        if ($afterRepairImageFile) {
            if ($repairRequest->after_image_path) { Storage::disk('public')->delete($repairRequest->after_image_path); }
            $dataToUpdate['after_image_path'] = $afterRepairImageFile->store('after_repair_images', 'public');
        } elseif ($clearAfterImage && $repairRequest->after_image_path) {
            Storage::disk('public')->delete($repairRequest->after_image_path);
            $dataToUpdate['after_image_path'] = null;
        }

        if ($updatingUser->is_admin || ($updatingUser->is_technician && $repairRequest->assigned_to_user_id === $updatingUser->id)) {
            if (isset($validatedData['status_id'])) {
                $dataToUpdate['status_id'] = $validatedData['status_id'];
                $this->handleCompletionLogic($repairRequest, $dataToUpdate, $validatedData['status_id'], $updatingUser, $validatedData['completed_at'] ?? null);
            }
            if (isset($validatedData['remarks_by_technician'])) {
                $dataToUpdate['remarks_by_technician'] = $validatedData['remarks_by_technician'];
            }
        }

        if ($updatingUser->is_admin) {
            if (array_key_exists('assigned_to_user_id', $validatedData)) {
                $dataToUpdate['assigned_to_user_id'] = $validatedData['assigned_to_user_id'];
            }
            if (isset($validatedData['completed_at']) && !array_key_exists('completed_at', $dataToUpdate)) {
                $dataToUpdate['completed_at'] = $validatedData['completed_at'];
            }
        }

        if (!empty($dataToUpdate)) {
            $repairRequest->update($dataToUpdate);
            $this->logActivityForUpdate($repairRequest, $updatingUser);
        }

        $updatedRequest = $repairRequest->refresh();

        $statusMessage = 'รายการแจ้งซ่อมได้รับการอัปเดตแล้ว!';
        if($statusChanged) {
            $statusMessage = 'อัปเดตสถานะเป็น "' . $updatedRequest->status->name . '" เรียบร้อยแล้ว';
        }

        return [
            'repairRequest' => $updatedRequest,
            'message' => $statusMessage
        ];
    }

    /**
     * อัปเดตสถานะ/มอบหมายงาน จากฟอร์ม Quick Update
     */
    public function updateStatusAndAssignment(RepairRequest $repairRequest, array $validatedData, User $updatingUser): RepairRequest
    {
        $dataToUpdate = [];
        if (isset($validatedData['status_id'])) {
            if ($updatingUser->is_admin || ($updatingUser->is_technician && $repairRequest->assigned_to_user_id === $updatingUser->id)) {
                $dataToUpdate['status_id'] = $validatedData['status_id'];
                $this->handleCompletionLogic($repairRequest, $dataToUpdate, $validatedData['status_id'], $updatingUser);
            }
        }
        if (isset($validatedData['remarks_by_technician'])) {
            if ($updatingUser->is_admin || ($updatingUser->is_technician && $repairRequest->assigned_to_user_id === $updatingUser->id)) {
                $dataToUpdate['remarks_by_technician'] = $validatedData['remarks_by_technician'];
            }
        }
        if ($updatingUser->is_admin && array_key_exists('assigned_to_user_id', $validatedData)) {
            $dataToUpdate['assigned_to_user_id'] = $validatedData['assigned_to_user_id'];
        }
        if (!empty($dataToUpdate)) {
            $repairRequest->update($dataToUpdate);
            $this->logActivityForUpdate($repairRequest, $updatingUser);
        }
        return $repairRequest->refresh();
    }

    /**
     * ลบรายการแจ้งซ่อม
     */
    public function deleteRepairRequest(RepairRequest $repairRequest, User $deletingUser): void
    {
        activity()->performedOn($repairRequest)->causedBy($deletingUser)->log('ลบรายการแจ้งซ่อม: ' . $repairRequest->title);
        if ($repairRequest->image_path) { Storage::disk('public')->delete($repairRequest->image_path); }
        if ($repairRequest->after_image_path) { Storage::disk('public')->delete($repairRequest->after_image_path); }
        $repairRequest->delete();
    }

    /**
     * จัดการ Logic การตั้งค่า completed_at
     */
    private function handleCompletionLogic(RepairRequest $repairRequest, array &$dataToUpdate, int $newStatusId, User $actor, ?string $submittedCompletedAt = null): void
    {
        $statusModel = Status::find($newStatusId);
        if ($statusModel && $statusModel->name === 'ซ่อมเสร็จสิ้น') {
            if (!$repairRequest->completed_at) { $dataToUpdate['completed_at'] = now(); }
            if (!is_null($submittedCompletedAt) && $actor->is_admin) { $dataToUpdate['completed_at'] = $submittedCompletedAt; }
        } elseif ($statusModel && $statusModel->name !== 'ซ่อมเสร็จสิ้น') {
            $dataToUpdate['completed_at'] = null;
        } elseif (!is_null($submittedCompletedAt) && $actor->is_admin) {
            $dataToUpdate['completed_at'] = $submittedCompletedAt;
        }
    }

    /**
     * สร้าง Activity Log สำหรับการอัปเดตข้อมูล
     */
    private function logActivityForUpdate(RepairRequest $repairRequest, User $updatingUser): void
    {
        if (!$repairRequest->wasChanged()) { return; }
        $changes = $repairRequest->getChanges();
        unset($changes['updated_at']);
        if (empty($changes)) { return; }
        $oldValues = array_intersect_key($repairRequest->getOriginal(), $changes);
        activity()->performedOn($repairRequest)->causedBy($updatingUser)->withProperties(['attributes' => $changes, 'old' => $oldValues])->log('อัปเดตข้อมูลรายการแจ้งซ่อม');
    }

    /**
     * ดึง Activity Logs สำหรับรายการแจ้งซ่อมที่ระบุ
     *
     * @param \App\Models\RepairRequest $repairRequest
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRepairRequestActivities(RepairRequest $repairRequest)
    {
        return Activity::where('subject_type', RepairRequest::class)
                       ->where('subject_id', $repairRequest->id)
                       ->with('causer')
                       ->latest()
                       ->get();
    }
}