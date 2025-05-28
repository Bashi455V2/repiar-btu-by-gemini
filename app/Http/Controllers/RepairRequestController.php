<?php

namespace App\Http\Controllers;

use App\Models\RepairRequest;
use App\Models\User; // ตรวจสอบให้แน่ใจว่ามีบรรทัดนี้อยู่แล้ว
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RepairRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::check()) {
            if (Auth::user()->is_admin || Auth::user()->is_technician) {
                // หากเป็นแอดมินหรือช่าง ให้ดึงรายการทั้งหมดไปแสดงที่นี่
                $repairRequests = RepairRequest::latest()->paginate(10);
            } else {
                // หากเป็นผู้ใช้งานทั่วไป ให้ดึงเฉพาะรายการที่ตัวเองแจ้งเข้ามา
                $repairRequests = Auth::user()->repairRequests()->latest()->paginate(10);
            }
        } else {
            return redirect()->route('login')->with('error', 'กรุณาเข้าสู่ระบบเพื่อดูรายการแจ้งซ่อม');
        }

        return view('repair_requests.index', compact('repairRequests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('repair_requests.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'contact_info' => 'nullable|string|max:255',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('attachments', 'public');
        }

        Auth::user()->repairRequests()->create([
            'subject' => $validatedData['subject'],
            'description' => $validatedData['description'],
            'location' => $validatedData['location'],
            'contact_info' => $validatedData['contact_info'],
            'attachment' => $attachmentPath,
            'status' => 'pending',
            'priority' => 'normal',
        ]);

        return redirect()->route('repair_requests.index')->with('success', 'แจ้งซ่อมสำเร็จแล้ว!');
    }

    /**
     * Display the specified resource.
     */
    public function show(RepairRequest $repairRequest)
    {
        if (Auth::user()->id !== $repairRequest->user_id && !Auth::user()->is_admin && !Auth::user()->is_technician) {
            abort(403); // Unauthorized
        }
        return view('repair_requests.show', compact('repairRequest'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RepairRequest $repairRequest)
    {
        if (Auth::user()->id !== $repairRequest->user_id && !Auth::user()->is_admin && !Auth::user()->is_technician) {
            abort(403); // Unauthorized
        }

        $technicians = User::where('is_technician', true)->get();

        return view('repair_requests.edit', compact('repairRequest', 'technicians'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RepairRequest $repairRequest)
    {
        if (Auth::user()->id !== $repairRequest->user_id && !Auth::user()->is_admin && !Auth::user()->is_technician) {
            abort(403); // Unauthorized
        }

        $validatedData = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'contact_info' => 'nullable|string|max:255',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'status' => 'nullable|string|in:pending,in_progress,completed,cancelled',
            'priority' => 'nullable|string|in:low,normal,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
            'completed_at' => 'nullable|date',
        ]);

        $repairRequest->subject = $validatedData['subject'];
        $repairRequest->description = $validatedData['description'];
        $repairRequest->location = $validatedData['location'];
        $repairRequest->contact_info = $validatedData['contact_info'];

        if ($request->hasFile('attachment')) {
            if ($repairRequest->attachment) {
                Storage::disk('public')->delete($repairRequest->attachment);
            }
            $repairRequest->attachment = $request->file('attachment')->store('attachments', 'public');
        } elseif ($request->boolean('clear_attachment')) {
            if ($repairRequest->attachment) {
                Storage::disk('public')->delete($repairRequest->attachment);
                $repairRequest->attachment = null;
            }
        }

        if (Auth::user()->is_admin || Auth::user()->is_technician) {
            if (isset($validatedData['status'])) {
                $repairRequest->status = $validatedData['status'];
            }

            if (isset($validatedData['priority'])) {
                $repairRequest->priority = $validatedData['priority'];
            }

            $repairRequest->assigned_to = $validatedData['assigned_to'] ?? null;

            if ($repairRequest->status === 'completed') {
                $repairRequest->completed_at = $validatedData['completed_at'] ?? now();
            } else {
                $repairRequest->completed_at = null;
            }
        }

        $repairRequest->save();

        return redirect()->route('repair_requests.index')->with('success', 'รายการแจ้งซ่อมได้รับการอัปเดตแล้ว!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RepairRequest $repairRequest)
    {
        if (Auth::user()->id !== $repairRequest->user_id && !Auth::user()->is_admin) {
            abort(403); // Unauthorized
        }

        if ($repairRequest->attachment) {
            Storage::disk('public')->delete($repairRequest->attachment);
        }

        $repairRequest->delete();

        return redirect()->route('repair_requests.index')->with('success', 'รายการแจ้งซ่อมถูกลบแล้ว!');
    }

    /**
     * Display a listing of repair requests for management by admin/technician.
     */
    public function manage()
    {

      

        // Middleware HasRole ('admin,technician') จะจัดการการอนุญาตสิทธิ์ที่ระดับ Route แล้ว
        if (!Auth::user()->is_admin && !Auth::user()->is_technician) {
            abort(403, 'คุณไม่ได้รับอนุญาตให้เข้าถึงหน้านี้');
        }

        // ดึงรายการแจ้งซ่อมทั้งหมดมาแสดงผล
        $repairRequests = RepairRequest::latest()->paginate(10);

        // <--- บรรทัดที่ถูกเพิ่ม/แก้ไขให้สมบูรณ์: ดึงรายชื่อช่างทั้งหมด
        $technicians = User::where('is_technician', true)->get();

        // ส่งทั้งรายการแจ้งซ่อมและรายชื่อช่างไปยัง View
        return view('repair_requests.manage', compact('repairRequests', 'technicians'));
    }

    /**
     * Update status, priority, assigned_to, and completed_at for a specific repair request.
     * This method is intended for quick updates, possibly from a list view.
     */
    public function updateStatusAndAssign(Request $request, RepairRequest $repairRequest)
    {
        if (!Auth::user()->is_admin && !Auth::user()->is_technician) {
            abort(403, 'คุณไม่ได้รับอนุญาตให้ดำเนินการนี้');
        }

        $validatedData = $request->validate([
            'status' => 'required|string|in:pending,in_progress,completed,cancelled',
            'priority' => 'required|string|in:low,normal,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
            'completed_at' => 'nullable|date',
        ]);

        $repairRequest->status = $validatedData['status'];
        $repairRequest->priority = $validatedData['priority'];
        $repairRequest->assigned_to = $validatedData['assigned_to'] ?? null;

        if ($repairRequest->status === 'completed') {
            $repairRequest->completed_at = $validatedData['completed_at'] ?? now();
        } else {
            $repairRequest->completed_at = null;
        }

        $repairRequest->save();

        return redirect()->route('repair_requests.manage')->with('success', 'สถานะและข้อมูลการมอบหมายได้รับการอัปเดตแล้ว!');
    }
}