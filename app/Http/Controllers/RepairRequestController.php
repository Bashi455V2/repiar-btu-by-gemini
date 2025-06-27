<?php

namespace App\Http\Controllers;

use App\Models\RepairRequest;
use App\Models\Category;
use App\Models\Location;
use App\Models\Status;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\StoreRepairRequest;
use App\Http\Requests\UpdateRepairRequest;
use App\Http\Requests\UpdateStatusAssignRequest;
use App\Services\RepairRequestService;
use Illuminate\Support\Facades\Auth;
// use Spatie\Activitylog\Models\Activity; // บรรทัดนี้ถูกต้องที่ลบออกไปแล้วครับ

class RepairRequestController extends Controller
{
    protected RepairRequestService $repairRequestService;

    public function __construct(RepairRequestService $repairRequestService)
    {
        $this->repairRequestService = $repairRequestService;
    }

    private function getCommonViewData()
    {
        // ควรเรียงลำดับ Status ตาม order field หรือลำดับที่ต้องการใน UI
        // หากไม่มี field 'order' คุณอาจจะต้องใช้ orderBy('id') หรือปรับ logic ใน Status model ให้คืนค่าตามลำดับที่ต้องการ
        return [
            'categories' => cache()->remember('categories', now()->addHour(), fn() => Category::orderBy('name')->get()),
            'locations' => cache()->remember('locations', now()->addHour(), fn() => Location::orderBy('name')->get()),
            // สมมติว่ามี field 'order' ในตาราง statuses เพื่อเรียงลำดับสถานะให้ถูกต้องตาม Workflow
            'statuses' => cache()->remember('statuses', now()->addHour(), fn() => Status::orderBy('order')->get()),
            // ดึงเฉพาะผู้ใช้ที่เป็นช่าง (is_technician = true)
            'technicians' => cache()->remember('technicians', now()->addHour(), fn() => User::where('is_technician', true)->orderBy('name')->get()),
        ];
    }

    /**
     * Display a listing of the resource for REGULAR USERS.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->is_admin) {
            return redirect()->route('admin.manage.index');
        }
        if ($user->is_technician) {
            return redirect()->route('technician.tasks.index');
        }

        $query = RepairRequest::with(['status', 'category', 'location'])->where('user_id', $user->id)->latest('id');
        $repairRequests = $query->paginate(10);
        return view('user.repair_info.index', array_merge(['repairRequests' => $repairRequests], $this->getCommonViewData()));
    }

    /**
     * Display "My Tasks" for TECHNICIANS.
     */
    public function technicianTasks(Request $request)
    {
        $this->authorize('viewAny', RepairRequest::class);
        $user = Auth::user();

        $query = RepairRequest::where('assigned_to_user_id', $user->id);

        $statusNames = [
            'new' => ['รอดำเนินการ', 'มอบหมายแล้ว'],
            'in_progress' => ['กำลังดำเนินการ', 'รออะไหล่'],
            'completed' => ['ซ่อมเสร็จสิ้น']
        ];

        // ดึง status IDs โดยใช้ `Status` model
        $allStatuses = Status::all()->keyBy('name');
        $statusIds = [
            'new' => $allStatuses->filter(fn($s) => in_array($s->name, $statusNames['new']))->pluck('id'),
            'in_progress' => $allStatuses->filter(fn($s) => in_array($s->name, $statusNames['in_progress']))->pluck('id'),
            'completed' => $allStatuses->filter(fn($s) => in_array($s->name, $statusNames['completed']))->pluck('id'),
        ];


        $currentStatusGroup = $request->input('status_group', 'new');

        // Clone query for counts BEFORE applying current status group filter
        $queryForCounts = clone $query;
        $taskCounts = [
            'new' => $queryForCounts->clone()->whereIn('status_id', $statusIds['new'])->count(),
            'in_progress' => $queryForCounts->clone()->whereIn('status_id', $statusIds['in_progress'])->count(),
            'completed' => $queryForCounts->clone()->whereIn('status_id', $statusIds['completed'])->count(),
        ];

        // Apply current status group filter to main query
        if (isset($statusIds[$currentStatusGroup])) {
            $query->whereIn('status_id', $statusIds[$currentStatusGroup]);
        }

        $repairRequests = $query->withDetails()->latest('id')->paginate(10);

        return view('technician.tasks.index', array_merge([
            'repairRequests' => $repairRequests,
            'currentStatusGroup' => $currentStatusGroup,
            'taskCounts' => $taskCounts,
        ], $this->getCommonViewData()));
    }

    /**
     * Display the job queue for TECHNICIANS (unassigned tasks and tasks by others).
     */
    public function technicianQueue(Request $request)
    {
        $this->authorize('viewAny', RepairRequest::class);
        $user = Auth::user();

        $query = RepairRequest::query();
        $currentQueueFilter = $request->input('filter', 'available');

        switch ($currentQueueFilter) {
            case 'available':
                // งานที่ยังไม่มีใครรับและสถานะเป็น "รอดำเนินการ"
                $pendingStatus = Status::where('name', 'รอดำเนินการ')->first();
                if ($pendingStatus) {
                    $query->whereNull('assigned_to_user_id')->where('status_id', $pendingStatus->id);
                } else {
                    $query->whereNull('assigned_to_user_id'); // Fallback if status not found
                }
                break;
            case 'others_assigned':
                // งานที่ถูกมอบหมายแล้ว แต่ไม่ใช่ของช่างคนปัจจุบัน
                $query->whereNotNull('assigned_to_user_id')
                      ->where('assigned_to_user_id', '!=', $user->id);
                break;
            case 'all': // แสดงงานทั้งหมดใน Queue (ทั้งที่ยังไม่ได้รับและที่คนอื่นรับไปแล้ว)
                // ไม่ต้องใส่เงื่อนไขเฉพาะเจาะจงที่นี่
                break;
        }

        $statusNames = [
            'new' => ['รอดำเนินการ', 'มอบหมายแล้ว'],
            'in_progress' => ['กำลังดำเนินการ', 'รออะไหล่'],
            'completed' => ['ซ่อมเสร็จสิ้น']
        ];

        // ดึง status IDs โดยใช้ `Status` model
        $allStatuses = Status::all()->keyBy('name');
        $statusIds = [
            'new' => $allStatuses->filter(fn($s) => in_array($s->name, $statusNames['new']))->pluck('id'),
            'in_progress' => $allStatuses->filter(fn($s) => in_array($s->name, $statusNames['in_progress']))->pluck('id'),
            'completed' => $allStatuses->filter(fn($s) => in_array($s->name, $statusNames['completed']))->pluck('id'),
        ];

        $currentStatusGroup = $request->input('status_group', 'new');

        // Clone query for counts AFTER applying queue filter (available/others_assigned/all)
        $queryForCounts = clone $query;
        $taskCounts = [
            'new' => $queryForCounts->clone()->whereIn('status_id', $statusIds['new'])->count(),
            'in_progress' => $queryForCounts->clone()->whereIn('status_id', $statusIds['in_progress'])->count(),
            'completed' => $queryForCounts->clone()->whereIn('status_id', $statusIds['completed'])->count(),
        ];

        // Apply current status group filter to main query
        if (isset($statusIds[$currentStatusGroup])) {
            $query->whereIn('status_id', $statusIds[$currentStatusGroup]);
        }

        $repairRequests = $query->withDetails()->latest('id')->paginate(10);

        return view('technician.queue.index', array_merge([
            'repairRequests' => $repairRequests,
            'viewType' => 'queue',
            'currentQueueFilter' => $currentQueueFilter,
            'currentStatusGroup' => $currentStatusGroup,
            'taskCounts' => $taskCounts,
        ], $this->getCommonViewData()));
    }

    /**
     * Assign the specified repair request to the currently authenticated technician.
     */
    public function claim(RepairRequest $repairRequest)
    {
        $this->authorize('claim', $repairRequest);
        if ($repairRequest->assigned_to_user_id) {
            return redirect()->back()->with('error', 'ขออภัย, งานนี้มีช่างรับไปแล้ว!');
        }
        $inProgressStatus = Status::where('name', 'กำลังดำเนินการ')->first();
        // หากต้องการให้สถานะเริ่มต้นเป็น 'มอบหมายแล้ว' เมื่อมีการรับงาน ให้เปลี่ยนตรงนี้
        // แต่ส่วนใหญ่เมื่อช่างรับงาน ก็จะเริ่ม 'กำลังดำเนินการ' ทันที
        // $assignedStatus = Status::where('name', 'มอบหมายแล้ว')->first();

        $repairRequest->update([
            'assigned_to_user_id' => Auth::id(),
            'status_id' => $inProgressStatus ? $inProgressStatus->id : $repairRequest->status_id, // ใช้กำลังดำเนินการ ถ้ามี ไม่งั้นใช้สถานะเดิม
        ]);
        // ใช้ activity() helper ซึ่งควรมีการกำหนดค่าไว้แล้ว (เช่น Spatie Activitylog)
        activity()->performedOn($repairRequest)->causedBy(Auth::user())->log('รับงานและเริ่มดำเนินการ');
        return redirect()->route('technician.tasks.index')->with('status', 'คุณได้รับงาน #' . $repairRequest->id . ' เรียบร้อยแล้ว');
    }


    public function create()
    {
        $this->authorize('create', RepairRequest::class);
        return view('repair_requests.create', $this->getCommonViewData());
    }

    public function store(StoreRepairRequest $request)
    {
        $this->authorize('create', RepairRequest::class);
        $this->repairRequestService->createRepairRequest(
            $request->validated(),
            $request->user(),
            $request->hasFile('image') ? $request->file('image') : null
        );
        return redirect()->route('repair_requests.index')->with('status', 'แจ้งซ่อมเรียบร้อยแล้ว!');
    }

    public function show(RepairRequest $repairRequest, Request $request)
    {
        $this->authorize('view', $repairRequest);

        $backUrlParams = $request->query();

        $activities = $this->repairRequestService->getRepairRequestActivities($repairRequest);

        return view('repair_requests.show', array_merge(
            compact('repairRequest', 'backUrlParams', 'activities'),
            $this->getCommonViewData()
        ));
    }

    public function edit(RepairRequest $repairRequest)
    {
        $this->authorize('update', $repairRequest);
        $repairRequest->load(['user', 'status', 'assignedTo', 'category', 'location']);
        return view('repair_requests.edit', array_merge(['repairRequest' => $repairRequest], $this->getCommonViewData()));
    }

    public function update(UpdateRepairRequest $request, RepairRequest $repairRequest)
    {
        $this->authorize('update', $repairRequest);

        $updateResult = $this->repairRequestService->updateRepairRequest(
            $repairRequest,
            $request->validated(),
            $request->user(),
            $request->hasFile('image') ? $request->file('image') : null,
            $request->boolean('clear_image'),
            $request->hasFile('after_repair_image') ? $request->file('after_repair_image') : null,
            $request->boolean('clear_after_image')
        );

        $user = $request->user();
        $redirectRouteName = 'repair_requests.show';
        $routeParameters = ['repair_request' => $repairRequest->id];

        // ตรวจสอบว่าผู้ใช้เป็น Admin หรือไม่ เพื่อ Redirect ไปยังหน้า manage
        if ($user->is_admin) {
            $redirectRouteName = 'admin.manage.index'; // เปลี่ยนเป็นชื่อ route ที่ใช้สำหรับหน้า admin.manage
            $routeParameters = []; // Admin manage ไม่จำเป็นต้องส่ง ID ของ repair request

            // สำคัญ: ส่ง query parameters ที่มาจากหน้า admin.manage กลับไปด้วย
            // เพื่อให้ filter และ pagination ยังคงอยู่หลังจาก redirect
            $routeParameters = array_merge($routeParameters, $request->query());
        }
        // ถ้าเป็น Technician หรือ User ทั่วไป ให้กลับไปที่หน้า show เดิม
        // และส่ง query parameters เดิมกลับไปด้วย เพื่อให้ปุ่มย้อนกลับทำงานได้
        $routeParameters = array_merge($routeParameters, $request->query());


        return redirect()->route($redirectRouteName, $routeParameters)->with('status', $updateResult['message']);
    }

    public function destroy(RepairRequest $repairRequest)
    {
        $this->authorize('delete', $repairRequest);
        $this->repairRequestService->deleteRepairRequest($repairRequest, Auth::user());
        return redirect()->route('admin.manage.index')->with('status', 'รายการแจ้งซ่อมถูกลบแล้ว!');
    }

    /**
     * Display a listing of repair requests for ADMINS to manage.
     */
    public function manage(Request $request)
    {
        $this->authorize('viewAny', RepairRequest::class);

        $query = RepairRequest::with(['user', 'status', 'assignedTo', 'category', 'location'])->latest('id');

        // Filter: สถานะการมอบหมาย (Assignment Status)
        $currentAssignmentFilter = $request->input('assignment_status', 'all');
        if ($currentAssignmentFilter === 'unassigned') {
            $query->whereNull('assigned_to_user_id');
        } elseif ($currentAssignmentFilter === 'assigned') {
            $query->whereNotNull('assigned_to_user_id');
        }

        // Filter: สถานะการแจ้งซ่อม (Repair Status)
        if ($request->filled('status_id')) {
            $query->where('status_id', $request->input('status_id'));
        }

        // Filter: ช่างที่รับผิดชอบ (Assigned Technician)
        if ($request->filled('assigned_to_user_id')) {
            if ($request->input('assigned_to_user_id') === 'unassigned_tech') {
                $query->whereNull('assigned_to_user_id');
            } else {
                $query->where('assigned_to_user_id', $request->input('assigned_to_user_id'));
            }
        }

        // Search: ค้นหา (Title or ID)
        if ($request->filled('search_query')) {
            $searchQuery = $request->input('search_query');
            $query->where(function ($q) use ($searchQuery) {
                $q->where('title', 'like', '%' . $searchQuery . '%')
                  ->orWhere('id', $searchQuery); // ค้นหาด้วย ID ตรงๆ
            });
        }

        $repairRequests = $query->paginate(15); // กำหนดจำนวนต่อหน้า

        $viewData = array_merge([
            'repairRequests' => $repairRequests,
            'currentAssignmentFilter' => $currentAssignmentFilter,
            // ไม่ต้องส่ง currentStatusFilter, currentTechnicianFilter, searchQuery
            // เพราะ request('name') ใน Blade จะจัดการให้เอง
        ], $this->getCommonViewData());

        // ตรวจสอบ View Path ใน Blade View ที่ปรับให้ไป
        return view('admin.manage.index', $viewData); // เปลี่ยนเป็น 'admin.manage'
    }

    public function updateStatusAndAssign(UpdateStatusAssignRequest $requestInput, RepairRequest $repairRequest)
    {
        $this->authorize('update', $repairRequest);
        $updatedRequest = $this->repairRequestService->updateStatusAndAssignment(
            $repairRequest,
            $requestInput->validated(),
            $requestInput->user()
        );

        if ($requestInput->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'อัปเดตรายการ ID #' . $updatedRequest->id . ' เรียบร้อยแล้ว!',
                'data' => [
                    'status' => ['name' => optional($updatedRequest->status)->name ?? 'N/A', 'color_class' => optional($updatedRequest->status)->color_class ?? 'bg-slate-200 text-slate-800'],
                    'assigned_to' => ['name' => optional($updatedRequest->assignedTo)->name ?? '-']
                ]
            ]);
        }

        // เมื่อทำการ update และ redirect กลับไปหน้า show, เราควรส่ง query parameters เดิมกลับไปด้วย
        // เพื่อให้ปุ่มย้อนกลับทำงานได้ถูกต้องหลังจากกดบันทึกในหน้า show
        // หรือถ้าต้องการให้กลับไปที่หน้าเดิมพร้อม Query parameters
        return redirect()->back()->with('status', 'อัปเดตรายการ ID #' . $updatedRequest->id . ' เรียบร้อยแล้ว!');
    }
}