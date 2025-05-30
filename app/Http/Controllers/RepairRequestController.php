<?php

namespace App\Http\Controllers;

use App\Models\RepairRequest;
use App\Models\Category;
use App\Models\Location;
use App\Models\Status;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
// use App\Http\Requests\StoreRepairRequest; // ตัวอย่างถ้าใช้ Form Request
// use App\Http\Requests\UpdateRepairRequest; // ตัวอย่างถ้าใช้ Form Request

class RepairRequestController extends Controller
{
    // กำหนด Middleware สำหรับ Authorization โดยใช้ Policies
    // public function __construct()
    // {
    //     // 'repair_request' คือชื่อ parameter ที่ใช้ใน route (เช่น Route::resource('repair_requests', ...))
    //     $this->authorizeResource(RepairRequest::class, 'repair_request');
    // }

    private function getCommonViewData()
    {
        return [
            'categories' => Category::orderBy('name')->get(),
            'locations' => Location::orderBy('name')->get(),
            'statuses' => Status::orderBy('name')->get(),
            'technicians' => User::where('is_technician', true)->orderBy('name')->get(),
        ];
    }

    public function index()
    {
        // $this->authorize('viewAny', RepairRequest::class); // ใช้ถ้า authorizeResource ไม่ได้ครอบคลุม หรือต้องการ logic พิเศษ
        $user = Auth::user();
        $query = RepairRequest::withDetails()->latest(); // สมมติว่ามี scopeWithDetails()

        if ($user->is_admin) {
            $repairRequests = $query->paginate(10);
        } elseif ($user->is_technician) {
            $repairRequests = $query->where(function($q) use ($user) {
                                    $q->where('assigned_to_user_id', $user->id)
                                      ->orWhereNull('assigned_to_user_id');
                                })->paginate(10);
        } else {
            $repairRequests = $user->repairRequests()
                                  ->withDetails() // สมมติว่ามี scopeWithDetails()
                                  ->latest()->paginate(10);
        }
        return view('repair_requests.index', compact('repairRequests'));
    }

    
     public function create()
    {
        $this->authorize('create', RepairRequest::class); // <--- บรรทัดที่เรียก authorize
        // ... (ส่วนที่เหลือของ create method) ...
        $categories = Category::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        return view('repair_requests.create', compact('categories', 'locations'));
    }

    public function store(Request $request) // ควรเปลี่ยนเป็น StoreRepairRequest $request ถ้าใช้ Form Request
    {
        $this->authorize('create', RepairRequest::class);

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location_id' => 'required|exists:locations,id',
            'category_id' => 'required|exists:categories,id',
            'requester_phone' => 'nullable|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('repair_images', 'public');
        }

        $defaultStatus = Status::where('name', 'รอดำเนินการ')->firstOrFail();

        Auth::user()->repairRequests()->create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'location_id' => $validatedData['location_id'],
            'category_id' => $validatedData['category_id'],
            'status_id' => $defaultStatus->id,
            'requester_phone' => $validatedData['requester_phone'] ?? (Auth::user()->phone_number ?? null),
            'image_path' => $imagePath,
        ]);

        return redirect()->route('repair_requests.index')->with('status', 'แจ้งซ่อมเรียบร้อยแล้ว!');
    }

    public function show(RepairRequest $repairRequest)
    {
        $this->authorize('view', $repairRequest);
        $repairRequest->loadDetails(); // สมมติว่ามี method loadDetails() ที่ใช้ $this->load(...)
        return view('repair_requests.show', compact('repairRequest'));
    }

    public function edit(RepairRequest $repairRequest)
    {
        $this->authorize('update', $repairRequest);
        $repairRequest->loadDetails();
        $viewData = array_merge(['repairRequest' => $repairRequest], $this->getCommonViewData());
        return view('repair_requests.edit', $viewData);
    }

    public function update(Request $request, RepairRequest $repairRequest)
    {
        $this->authorize('update', $repairRequest); // ใช้ Policy ตรวจสอบสิทธิ์ในการเริ่มอัปเดต

        $user = Auth::user();

        $validationRules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location_id' => 'required|exists:locations,id',
            'category_id' => 'required|exists:categories,id',
            'requester_phone' => 'nullable|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'clear_image' => 'nullable|boolean',
        ];

        // เพิ่ม Validation Rules สำหรับ fields ที่ Admin/Technician เท่านั้นที่แก้ไขได้
        if ($user->is_admin || $user->is_technician) {
            $validationRules['status_id'] = ['required', 'exists:statuses,id'];
            // ทดลองใช้ Rule ที่ง่ายกว่านี้ก่อนเพื่อทดสอบปัญหา "The selected assigned to user id is invalid."
            $validationRules['assigned_to_user_id'] = ['nullable', 'exists:users,id'];
            // ถ้า Rule ข้างบนทำงานได้ดี ค่อยลองกลับไปใช้ Rule เดิมที่ซับซ้อนกว่า:
            // $validationRules['assigned_to_user_id'] = ['nullable', 'exists:users,id,is_technician,true'];
            $validationRules['remarks_by_technician'] = ['nullable', 'string'];
            $validationRules['completed_at'] = ['nullable', 'date'];
        }

        $validatedData = $request->validate($validationRules);

        // ข้อมูลที่ User ทุกคน (ที่มีสิทธิ์ update ตาม Policy) สามารถแก้ไขได้
        $dataToUpdate = [
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'location_id' => $validatedData['location_id'],
            'category_id' => $validatedData['category_id'],
            'requester_phone' => $validatedData['requester_phone'] ?? $repairRequest->requester_phone, // เก็บค่าเดิมถ้าไม่ได้ส่งมา
        ];

        // จัดการรูปภาพ
        if ($request->hasFile('image')) {
            if ($repairRequest->image_path) {
                Storage::disk('public')->delete($repairRequest->image_path);
            }
            $dataToUpdate['image_path'] = $request->file('image')->store('repair_images', 'public');
        } elseif ($request->boolean('clear_image') && $repairRequest->image_path) {
            Storage::disk('public')->delete($repairRequest->image_path);
            $dataToUpdate['image_path'] = null;
        }

        // ข้อมูลที่ Admin/Technician เท่านั้นที่แก้ไขได้
        if ($user->is_admin || $user->is_technician) {
            if (isset($validatedData['status_id'])) {
                $dataToUpdate['status_id'] = $validatedData['status_id'];
                $statusModel = Status::find($validatedData['status_id']);
                if ($statusModel && $statusModel->name === 'ซ่อมเสร็จสิ้น' && !$repairRequest->completed_at) {
                    $dataToUpdate['completed_at'] = now();
                } elseif ($statusModel && $statusModel->name !== 'ซ่อมเสร็จสิ้น') {
                    // ถ้าสถานะไม่ใช่ "ซ่อมเสร็จสิ้น" และมีการส่ง completed_at มา ให้ใช้ค่าที่ส่งมา
                    // ถ้าไม่ได้ส่ง completed_at มา และสถานะไม่ใช่ "ซ่อมเสร็จสิ้น" ให้ตั้งเป็น null
                    $dataToUpdate['completed_at'] = $validatedData['completed_at'] ?? null;
                }
            }

            // ใช้ array_key_exists เพื่อให้สามารถตั้งค่าเป็น null ได้ (ยกเลิกการมอบหมาย)
            if (array_key_exists('assigned_to_user_id', $validatedData)) {
                $dataToUpdate['assigned_to_user_id'] = $validatedData['assigned_to_user_id'];
            }

            if (isset($validatedData['remarks_by_technician'])) {
                $dataToUpdate['remarks_by_technician'] = $validatedData['remarks_by_technician'];
            }

            // ถ้ามีการส่ง completed_at มาโดยตรง และยังไม่ได้ถูกจัดการโดย logic ของ status_id
            if (isset($validatedData['completed_at']) && !array_key_exists('completed_at', $dataToUpdate)) {
                 $dataToUpdate['completed_at'] = $validatedData['completed_at'];
            }
        }

        $repairRequest->update($dataToUpdate);

        // พิจารณา redirect ไปหน้าที่เหมาะสม
        $redirectRoute = ($user->is_admin || $user->is_technician) ? 'repair_requests.manage' : 'repair_requests.show';
        return redirect()->route($redirectRoute, $repairRequest)->with('status', 'รายการแจ้งซ่อมได้รับการอัปเดตเรียบร้อยแล้ว!');
    }

    public function destroy(RepairRequest $repairRequest)
    {
        $this->authorize('delete', $repairRequest);
        if ($repairRequest->image_path) {
            Storage::disk('public')->delete($repairRequest->image_path);
        }
        $repairRequest->delete();
        return redirect()->route('repair_requests.manage')->with('status', 'รายการแจ้งซ่อมถูกลบแล้ว!');
    }

    public function manage()
    {
        // Middleware HasRole จัดการสิทธิ์ที่ Route แล้ว
        $repairRequests = RepairRequest::withDetails()->latest()->paginate(15);
        $viewData = array_merge(['repairRequests' => $repairRequests], $this->getCommonViewData());
        return view('repair_requests.manage', $viewData);
    }

    public function updateStatusAndAssign(Request $requestInput, RepairRequest $repairRequest)
    {
        // Middleware HasRole จัดการสิทธิ์ที่ Route แล้ว
        // $this->authorize('manageRequests', RepairRequest::class); // หรือใช้ Policy method ที่สร้างขึ้นเอง

        $validatedData = $requestInput->validate([
            'status_id' => 'required|exists:statuses,id',
            'assigned_to_user_id' => 'nullable|exists:users,id',
            'remarks_by_technician' => 'nullable|string',
        ]);

        $dataToUpdate = [
            'status_id' => $validatedData['status_id'],
            'assigned_to_user_id' => $validatedData['assigned_to_user_id'] ?? null,
            'remarks_by_technician' => $validatedData['remarks_by_technician'] ?? $repairRequest->remarks_by_technician,
        ];

        $statusModel = Status::find($validatedData['status_id']);
        if ($statusModel && $statusModel->name === 'ซ่อมเสร็จสิ้น' && !$repairRequest->completed_at) {
            $dataToUpdate['completed_at'] = now();
        } elseif ($statusModel && $statusModel->name !== 'ซ่อมเสร็จสิ้น') {
            $dataToUpdate['completed_at'] = null;
        }

        $repairRequest->update($dataToUpdate);

        $redirectRoute = $requestInput->input('redirect_to_manage', true) ? 'repair_requests.manage' : 'repair_requests.show';
        return redirect()->route($redirectRoute, $repairRequest->id)->with('status', 'สถานะและข้อมูลการมอบหมายได้รับการอัปเดตแล้ว!');
    }
}