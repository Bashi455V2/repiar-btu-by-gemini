<?php

namespace App\Http\Controllers;

use App\Models\RepairRequest;
use App\Models\Category;
use App\Models\Location;
use App\Models\Status;
use App\Models\User;
use Illuminate\Http\Request; // ยังคงจำเป็นสำหรับ index() และ manage() methods
use App\Http\Requests\StoreRepairRequest;
use App\Http\Requests\UpdateRepairRequest;
use App\Http\Requests\UpdateStatusAssignRequest;
use App\Services\RepairRequestService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
// use Illuminate\Validation\Rule; // ถ้าไม่ใช้แล้วใน Controller นี้ สามารถลบออกได้

class RepairRequestController extends Controller
{
    protected RepairRequestService $repairRequestService;

    public function __construct(RepairRequestService $repairRequestService)
    {
        $this->repairRequestService = $repairRequestService;
        // พิจารณาเปิดใช้งาน ถ้า Policy methods ของคุณครบถ้วนตาม Resource actions
        // และคุณต้องการให้ Laravel จัดการ authorize โดยอัตโนมัติสำหรับ standard resource methods
        // $this->authorizeResource(RepairRequest::class, 'repair_request');
    }

    private function getCommonViewData()
    {
        return [
            'categories' => Category::orderBy('name')->get(),
            'locations' => Location::orderBy('name')->get(),
            'statuses' => Status::orderBy('name')->get(),
            'technicians' => User::where('is_technician', true)->orderBy('name')->get(),
            'assignmentStatuses' => [
                'all' => 'งานทั้งหมด (Admin)',
                'unassigned' => 'งานที่ยังไม่ได้มอบหมาย (Admin)',
                'assigned' => 'งานที่มอบหมายแล้ว (Admin)',
            ],
            'technicianTaskFiltersOpt' => [
                'my_tasks' => 'งานที่ฉันได้รับมอบหมาย',
                'all_tech_view' => 'งานทั้งหมดที่ฉันเห็นได้',
            ]
        ];
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = RepairRequest::withDetails()->latest();
        $commonData = $this->getCommonViewData();

        if ($user->is_admin) {
            return redirect()->route('admin.manage');
        } elseif ($user->is_technician) {
            $currentTechnicianTaskFilter = $request->input('technician_task_filter', 'my_tasks');
            if ($currentTechnicianTaskFilter === 'my_tasks') {
                $query->where('assigned_to_user_id', $user->id);
            } elseif ($currentTechnicianTaskFilter === 'all_tech_view') {
                $query->where(function($q) use ($user) {
                    $q->where('assigned_to_user_id', $user->id)
                      ->orWhereNull('assigned_to_user_id');
                });
            }
            $repairRequests = $query->paginate(10);
            $viewDataForTechnician = array_merge([
                'repairRequests' => $repairRequests,
                'currentTechnicianTaskFilter' => $currentTechnicianTaskFilter,
                'technicianTaskFilters' => $commonData['technicianTaskFiltersOpt']
            ], $commonData);
            return view('technician.tasks.index', $viewDataForTechnician);
        } else { // Regular User
            $query->where('user_id', $user->id);
            $repairRequests = $query->paginate(10);
            $viewDataForUser = array_merge(['repairRequests' => $repairRequests], $commonData);
            return view('user.repair_info.index', $viewDataForUser);
        }
    }

    public function create()
    {
        $this->authorize('create', RepairRequest::class);
        $viewData = $this->getCommonViewData();
        return view('repair_requests.create', [
            'categories' => $viewData['categories'],
            'locations' => $viewData['locations']
        ]);
    }

    public function store(StoreRepairRequest $request)
    {
        $this->authorize('create', RepairRequest::class);
        $validatedData = $request->validated();
        $imageFile = $request->hasFile('image') ? $request->file('image') : null;

        $this->repairRequestService->createRepairRequest(
            $validatedData,
            $request->user(),
            $imageFile
        );
        return redirect()->route('repair_requests.index')->with('status', 'แจ้งซ่อมเรียบร้อยแล้ว!');
    }

    public function show(RepairRequest $repairRequest)
    {
        $this->authorize('view', $repairRequest);
        $repairRequest->loadDetails(); // สมมติว่ามีการ Eager load relationships ที่จำเป็นใน scope นี้
        $activities = $repairRequest->activities()->latest()->paginate(10);
        $commonData = $this->getCommonViewData();
        // ส่ง $statuses และ $technicians ไปให้ show view ด้วย สำหรับฟอร์ม Quick Update
        return view('repair_requests.show', array_merge(compact('repairRequest', 'activities'), $commonData));
    }

    public function edit(RepairRequest $repairRequest)
    {
        $this->authorize('update', $repairRequest);
        $repairRequest->loadDetails();
        $viewData = array_merge(['repairRequest' => $repairRequest], $this->getCommonViewData());
        return view('repair_requests.edit', $viewData);
    }

    public function update(UpdateRepairRequest $request, RepairRequest $repairRequest)
    {
        $this->authorize('update', $repairRequest);
        $validatedData = $request->validated();
        $user = $request->user();

        $dataToUpdate = [
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'location_id' => $validatedData['location_id'],
            'category_id' => $validatedData['category_id'],
            'requester_phone' => $validatedData['requester_phone'] ?? $repairRequest->requester_phone,
        ];

        if ($request->hasFile('image')) {
            if ($repairRequest->image_path) { Storage::disk('public')->delete($repairRequest->image_path); }
            $dataToUpdate['image_path'] = $request->file('image')->store('repair_images', 'public');
        } elseif ($request->boolean('clear_image') && $repairRequest->image_path) {
            Storage::disk('public')->delete($repairRequest->image_path);
            $dataToUpdate['image_path'] = null;
        }

        if ($user->is_admin || ($user->is_technician && $repairRequest->assigned_to_user_id === $user->id)) {
            if (isset($validatedData['status_id'])) {
                $dataToUpdate['status_id'] = $validatedData['status_id'];
                $this->handleCompletionLogic($repairRequest, $dataToUpdate, $validatedData['status_id'], $validatedData['completed_at'] ?? null);
            }
            if (isset($validatedData['remarks_by_technician'])) {
                $dataToUpdate['remarks_by_technician'] = $validatedData['remarks_by_technician'];
            }
        }

        if ($user->is_admin) {
            if (array_key_exists('assigned_to_user_id', $validatedData)) {
                $dataToUpdate['assigned_to_user_id'] = $validatedData['assigned_to_user_id'];
            }
            if (isset($validatedData['completed_at']) && !array_key_exists('completed_at', $dataToUpdate)) {
                $dataToUpdate['completed_at'] = $validatedData['completed_at'];
            }
        }

        if (!empty($dataToUpdate)) {
            $repairRequest->update($dataToUpdate);
             // Activity Log สำหรับการ Update สามารถเพิ่มที่นี่ หรือใช้ Model Events/Observer
        }


        $redirectRouteName = 'repair_requests.show';
        $routeParameters = ['repair_request' => $repairRequest->id];
        if ($user->is_admin) {
            $redirectRouteName = 'repair_requests.manage';
            $routeParameters = [];
        } elseif ($user->is_technician) {
            // $redirectRouteName = 'technician.tasks.index'; $routeParameters = [];
        }
        return redirect()->route($redirectRouteName, $routeParameters)->with('status', 'รายการแจ้งซ่อมได้รับการอัปเดตแล้ว!');
    }

    public function destroy(RepairRequest $repairRequest)
    {
        $this->authorize('delete', $repairRequest);
        if ($repairRequest->image_path) {
            Storage::disk('public')->delete($repairRequest->image_path);
        }
        $repairRequest->delete();
        // Activity Log สำหรับการ Delete สามารถเพิ่มที่นี่ หรือใช้ Model Events/Observer
        return redirect()->route('repair_requests.manage')->with('status', 'รายการแจ้งซ่อมถูกลบแล้ว!');
    }

    public function manage(Request $request)
    {
        $this->authorize('viewAny', RepairRequest::class); // หรือ 'manage' ถ้ามีใน Policy และ User มีสิทธิ์
        $user = Auth::user();
        $query = RepairRequest::withDetails()->latest();
        $currentAssignmentFilter = $request->input('assignment_status', 'all');

        if ($currentAssignmentFilter === 'unassigned') {
            $query->whereNull('assigned_to_user_id');
        } elseif ($currentAssignmentFilter === 'assigned') {
            $query->whereNotNull('assigned_to_user_id');
        }

        $repairRequests = $query->paginate(15);
        $commonData = $this->getCommonViewData();
        $viewData = array_merge(['repairRequests' => $repairRequests, 'currentAssignmentFilter' => $currentAssignmentFilter], $commonData);
        return view('admin.manage.index', $viewData);
    }

    public function updateStatusAndAssign(UpdateStatusAssignRequest $requestInput, RepairRequest $repairRequest)
    {
        $this->authorize('update', $repairRequest); // แนะนำให้มีการ authorize ที่นี่

        $validatedData = $requestInput->validated();
        $user = $requestInput->user();

        $oldAssigneeId = $repairRequest->assigned_to_user_id;
        $oldStatusId = $repairRequest->status_id;
        $dataToUpdate = [];

        if (isset($validatedData['status_id'])) {
            if ($user->is_admin || ($user->is_technician && $repairRequest->assigned_to_user_id === $user->id)) {
                $dataToUpdate['status_id'] = $validatedData['status_id'];
                // เรียกใช้ Helper Method (ฟอร์ม Quick Update อาจจะไม่มี completed_at input โดยตรง)
                $this->handleCompletionLogic($repairRequest, $dataToUpdate, $validatedData['status_id']);
            }
        }

        if (isset($validatedData['remarks_by_technician'])) {
            if ($user->is_admin || ($user->is_technician && $repairRequest->assigned_to_user_id === $user->id)) {
                $dataToUpdate['remarks_by_technician'] = $validatedData['remarks_by_technician'];
            }
        }

        if ($user->is_admin && array_key_exists('assigned_to_user_id', $validatedData)) {
            $dataToUpdate['assigned_to_user_id'] = $validatedData['assigned_to_user_id'];
        }

        if (!empty($dataToUpdate)) {
            $repairRequest->update($dataToUpdate);

            // Activity Logging
            if ($repairRequest->wasChanged('assigned_to_user_id')) {
                $newAssignee = $repairRequest->assignedTo()->first();
                $oldAssigneeName = optional(User::find($oldAssigneeId))->name ?? 'N/A';
                $newAssigneeName = optional($newAssignee)->name ?? (is_null($repairRequest->assigned_to_user_id) ? 'ไม่มีผู้รับผิดชอบ' : 'N/A');
                $actionDescription = is_null($newAssignee) && !is_null($oldAssigneeId) ?
                    "ยกเลิกการมอบหมายงาน (เดิม: " . $oldAssigneeName . ")" :
                    "มอบหมายงานให้ช่าง: " . $newAssigneeName . ($oldAssigneeId && $oldAssigneeId != $repairRequest->assigned_to_user_id ? " (เดิม: " . $oldAssigneeName . ")" : "");
                activity()->performedOn($repairRequest)->causedBy($user)
                    ->withProperties(['old_assignee_id' => $oldAssigneeId, 'new_assignee_id' => $repairRequest->assigned_to_user_id])
                    ->log($actionDescription);
            }
            if ($repairRequest->wasChanged('status_id')) {
                $newStatus = $repairRequest->status()->first();
                $oldStatusName = optional(Status::find($oldStatusId))->name ?? 'N/A';
                $newStatusName = optional($newStatus)->name ?? 'N/A';
                activity()->performedOn($repairRequest)->causedBy($user)
                    ->withProperties(['old_status' => $oldStatusName, 'new_status' => $newStatusName])
                    ->log("เปลี่ยนสถานะเป็น: " . $newStatusName . ($oldStatusId && $oldStatusId != $repairRequest->status_id ? " (เดิม: " . $oldStatusName . ")" : ""));
            }
        }

        // Redirect logic
        $redirectToManageInput = $requestInput->input('redirect_to_manage', '0');
        $redirectRouteName = 'repair_requests.show';
        $routeParameters = ['repair_request' => $repairRequest->id];
        if ($redirectToManageInput == '1' && $user->is_admin) {
            $redirectRouteName = 'repair_requests.manage';
            $routeParameters = [];
        }
        return redirect()->back()->with('status', 'อัปเดตรายการ ID #' . $repairRequest->id . ' เรียบร้อยแล้ว!');
    }

    private function handleCompletionLogic(RepairRequest $repairRequest, array &$dataToUpdate, int $newStatusId, ?string $submittedCompletedAt = null): void
    {
        $statusModel = Status::find($newStatusId);
        if ($statusModel && $statusModel->name === 'ซ่อมเสร็จสิ้น') {
            if (!$repairRequest->completed_at) {
                $dataToUpdate['completed_at'] = now();
            }
            if (!is_null($submittedCompletedAt) && Auth::user() && Auth::user()->is_admin) {
                $dataToUpdate['completed_at'] = $submittedCompletedAt;
            }
        } elseif ($statusModel && $statusModel->name !== 'ซ่อมเสร็จสิ้น') {
            $dataToUpdate['completed_at'] = null;
        } elseif (!is_null($submittedCompletedAt) && Auth::user() && Auth::user()->is_admin) {
            $dataToUpdate['completed_at'] = $submittedCompletedAt;
        }
    }
}