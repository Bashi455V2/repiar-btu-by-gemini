<?php

namespace App\Services;

use App\Models\RepairRequest;
use App\Models\Status;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;    // สำหรับ handleCompletionLogic ถ้ายังอ้างอิง user ปัจจุบันโดยตรง
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str; // เพิ่มถ้าจะใช้ Str::limit ใน Activity Log

class RepairRequestService
{
    /**
     * สร้างรายการแจ้งซ่อมใหม่
     * (โค้ดเหมือนเดิมที่คุณส่งมา)
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
            ->log('สร้างรายการแจ้งซ่อม: ' . Str::limit($repairRequest->title, 50)); // ใช้ Str::limit

        return $repairRequest;
    }
public function update(UpdateRepairRequest $request, RepairRequest $repairRequest)
{
    $this->authorize('update', $repairRequest); // ตรวจสอบสิทธิ์ด้วย Policy

    $validatedData = $request->validated();
    $imageFile = $request->hasFile('image') ? $request->file('image') : null;
    $clearImage = $request->boolean('clear_image');

    // เรียกใช้ Service method เพื่ออัปเดตข้อมูล
    $this->repairRequestService->updateRepairRequest(
        $repairRequest,
        $validatedData,
        $request->user(), // User ที่กำลัง Login อยู่ (ผู้ทำการอัปเดต)
        $imageFile,
        $clearImage
    );

    // Redirect Logic (เหมือนเดิมที่แก้ไขแล้ว)
    $redirectRouteName = 'repair_requests.show';
    $routeParameters = ['repair_request' => $repairRequest->id];
    $user = $request->user();

    if ($user->is_admin) {
        $redirectRouteName = 'admin.manage';
        $routeParameters = [];
    } elseif ($user->is_technician) {
        // พิจารณา Technician redirect (ปัจจุบันคือไปหน้า show)
        // $redirectRouteName = 'technician.tasks.index'; $routeParameters = [];
    }

    return redirect()->route($redirectRouteName, $routeParameters)
                     ->with('status', 'รายการแจ้งซ่อมได้รับการอัปเดตแล้ว!');
}
    // ... (method createRepairRequest และ private method handleCompletionLogic ที่มีอยู่แล้ว) ...

    /**
     * อัปเดตรายการแจ้งซ่อมที่มีอยู่
     *
     * @param RepairRequest $repairRequest Instance ของงานซ่อมที่จะอัปเดต
     * @param array $validatedData ข้อมูลที่ผ่านการ Validate แล้วจาก UpdateRepairRequest
     * @param User $updatingUser User ผู้ทำการอัปเดต
     * @param UploadedFile|null $imageFile ไฟล์รูปภาพใหม่ (ถ้ามี)
     * @param bool $clearImage ระบุว่าจะลบรูปภาพปัจจุบันหรือไม่
     * @return RepairRequest รายการแจ้งซ่อมที่อัปเดตแล้ว
     */
    public function updateRepairRequest(
        RepairRequest $repairRequest,
        array $validatedData,
        User $updatingUser,
        ?UploadedFile $imageFile = null,
        bool $clearImage = false
    ): RepairRequest {
        $dataToUpdate = [
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'location_id' => $validatedData['location_id'],
            'category_id' => $validatedData['category_id'],
            'requester_phone' => $validatedData['requester_phone'] ?? $repairRequest->requester_phone,
        ];

        if ($imageFile) {
            if ($repairRequest->image_path) {
                Storage::disk('public')->delete($repairRequest->image_path);
            }
            $dataToUpdate['image_path'] = $imageFile->store('repair_images', 'public');
        } elseif ($clearImage && $repairRequest->image_path) {
            Storage::disk('public')->delete($repairRequest->image_path);
            $dataToUpdate['image_path'] = null;
        }

        // ส่วนที่ Admin หรือ Technician ที่ Assign เท่านั้นที่สามารถอัปเดตได้
        // Policy และ Form Request ควรจะกรองมาแล้วว่า $validatedData มี key เหล่านี้หรือไม่ตามสิทธิ์
        // แต่เราสามารถเช็ค Role ของ $updatingUser เพื่อความแน่นอนในการ "ใช้" ข้อมูลนั้นๆ
        if ($updatingUser->is_admin || ($updatingUser->is_technician && $repairRequest->assigned_to_user_id === $updatingUser->id)) {
            if (isset($validatedData['status_id'])) {
                $dataToUpdate['status_id'] = $validatedData['status_id'];
                // เรียกใช้ helper method ภายใน Service นี้
                $this->handleCompletionLogic(
                    $repairRequest,
                    $dataToUpdate,
                    $validatedData['status_id'],
                    $updatingUser, // ส่ง User ที่กระทำ Action ไป
                    $validatedData['completed_at'] ?? null
                );
            }
            if (isset($validatedData['remarks_by_technician'])) {
                $dataToUpdate['remarks_by_technician'] = $validatedData['remarks_by_technician'];
            }
        }

        // ส่วนที่ Admin เท่านั้นที่ทำได้
        if ($updatingUser->is_admin) {
            if (array_key_exists('assigned_to_user_id', $validatedData)) {
                $dataToUpdate['assigned_to_user_id'] = $validatedData['assigned_to_user_id'];
            }
            // ถ้า completed_at ถูกส่งมาโดยตรงจาก Admin และยังไม่ได้ถูกตั้งค่าโดย handleCompletionLogic
            if (isset($validatedData['completed_at']) && !array_key_exists('completed_at', $dataToUpdate)) {
                $dataToUpdate['completed_at'] = $validatedData['completed_at'];
            }
        }

        $updated = false;
        if (!empty($dataToUpdate)) {
            $updated = $repairRequest->update($dataToUpdate);
        }

        // Log Activity เฉพาะเมื่อมีการเปลี่ยนแปลงข้อมูลจริงๆ
        // และ $repairRequest->wasChanged() อาจจะไม่ทำงานตามที่คาดหวังเสมอไปหลัง $repairRequest->update() ถ้าไม่มีการเปลี่ยนแปลง
        // ควรจะเช็คจาก $updated หรือ $repairRequest->getChanges()
        if ($updated && count($repairRequest->getChanges()) > 0) {
            $changes = $repairRequest->getChanges();
            unset($changes['updated_at']); // ไม่นับ updated_at ที่เปลี่ยนอัตโนมัติ

            if (!empty($changes)) {
                 activity()
                     ->performedOn($repairRequest)
                     ->causedBy($updatingUser)
                     ->withProperties(['attributes' => $changes, 'old' => array_intersect_key($repairRequest->getOriginal(), $changes)])
                     ->log('อัปเดตข้อมูลรายการแจ้งซ่อม ID: ' . $repairRequest->id);
            }
        }

        return $repairRequest->refresh(); // คืนค่า Model ที่สดใหม่ (ถ้ามีการ update)
    }

    /**
     * จัดการ Logic การตั้งค่า completed_at โดยอ้างอิงจาก status_id ใหม่
     * และค่า completed_at ที่อาจจะถูกส่งมาโดยตรง
     * Method นี้ควรเป็น private เพราะจะถูกเรียกใช้ภายใน Service นี้เท่านั้น
     *
     * @param RepairRequest $repairRequest Instance ของงานซ่อมปัจจุบัน
     * @param array         &$dataToUpdate Array ของข้อมูลที่จะอัปเดต (ส่งแบบ Reference)
     * @param int           $newStatusId   ID ของสถานะใหม่
     * @param User          $actor         User ผู้กระทำการ (สำหรับตรวจสอบสิทธิ์ เช่น is_admin)
     * @param string|null   $submittedCompletedAt ค่า completed_at ที่อาจจะถูกส่งมาจากฟอร์มโดยตรง
     */
    private function handleCompletionLogic(RepairRequest $repairRequest, array &$dataToUpdate, int $newStatusId, User $actor, ?string $submittedCompletedAt = null): void
    {
        $statusModel = Status::find($newStatusId);

        if ($statusModel && $statusModel->name === 'ซ่อมเสร็จสิ้น') {
            if (!$repairRequest->completed_at) { // ถ้ายังไม่เคยมีวันที่ซ่อมเสร็จ ให้ตั้งเป็นเวลาปัจจุบัน
                $dataToUpdate['completed_at'] = now();
            }
            // ถ้า Admin ส่งค่า completed_at มาโดยตรง และสถานะเป็น "ซ่อมเสร็จสิ้น" ให้ใช้ค่าที่ Admin ส่งมา (เป็นการ override)
            if (!is_null($submittedCompletedAt) && $actor->is_admin) {
                $dataToUpdate['completed_at'] = $submittedCompletedAt;
            }
        } elseif ($statusModel && $statusModel->name !== 'ซ่อมเสร็จสิ้น') {
            // ถ้าสถานะใหม่ไม่ใช่ "ซ่อมเสร็จสิ้น" ให้ล้างค่า completed_at
            $dataToUpdate['completed_at'] = null;
        } elseif (!is_null($submittedCompletedAt) && $actor->is_admin) {
            // กรณีที่สถานะใหม่ไม่ชัดเจน (ไม่ใช่ "ซ่อมเสร็จสิ้น") แต่ Admin ส่ง completed_at มาโดยตรง
            $dataToUpdate['completed_at'] = $submittedCompletedAt;
        }
    }

    // เราจะเพิ่ม method updateRepairRequest และ updateStatusAndAssignmentForRequest ที่นี่ต่อไป
}