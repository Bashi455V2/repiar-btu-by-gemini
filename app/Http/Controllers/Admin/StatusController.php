<?php

namespace App\Http\Controllers\Admin; // <--- Namespace Admin

use App\Http\Controllers\Controller;
use App\Models\Status; // <--- Import Status Model
use Illuminate\Http\Request;

class StatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $statuses = Status::orderBy('name')->paginate(10);
        return view('admin.statuses.index', compact('statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.statuses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:statuses,name',
            'color_class' => 'nullable|string|max:255', // เช่น 'bg-yellow-100 text-yellow-800'
        ]);

        Status::create($validatedData);

        return redirect()->route('admin.statuses.index')->with('status', 'สถานะถูกเพิ่มเรียบร้อยแล้ว');
    }

    /**
     * Display the specified resource.
     */
    public function show(Status $status)
    {
        return redirect()->route('admin.statuses.edit', $status); // Redirect ไปหน้า edit
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Status $status)
    {
        return view('admin.statuses.edit', compact('status'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Status $status)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:statuses,name,' . $status->id,
            'color_class' => 'nullable|string|max:255',
        ]);

        $status->update($validatedData);

        return redirect()->route('admin.statuses.index')->with('status', 'ข้อมูลสถานะถูกอัปเดตเรียบร้อยแล้ว');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Status $status)
    {
        // เพิ่มการตรวจสอบก่อนลบ: สถานะนี้มีการใช้งานใน Repair Requests หรือไม่
        if ($status->repairRequests()->count() > 0) {
            return redirect()->route('admin.statuses.index')
                             ->with('error', 'ไม่สามารถลบสถานะนี้ได้ เนื่องจากมีการใช้งานอยู่ในการแจ้งซ่อม');
        }

        $status->delete();
        return redirect()->route('admin.statuses.index')->with('status', 'สถานะถูกลบเรียบร้อยแล้ว');
    }
}