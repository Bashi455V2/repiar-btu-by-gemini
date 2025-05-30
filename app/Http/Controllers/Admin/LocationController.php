<?php

namespace App\Http\Controllers\Admin; // <--- Namespace จะเป็น Admin

use App\Http\Controllers\Controller; // <--- Extends Controller หลัก
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $locations = Location::orderBy('name')->paginate(10); // หรือ orderBy('created_at', 'desc')
        return view('admin.locations.index', compact('locations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.locations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:locations,name',
            'building' => 'nullable|string|max:255',
            'floor' => 'nullable|string|max:50',
            'room_number' => 'nullable|string|max:50',
            'details' => 'nullable|string',
        ]);

        Location::create($validatedData);

        return redirect()->route('admin.locations.index')->with('status', 'สถานที่ถูกเพิ่มเรียบร้อยแล้ว');
    }

    /**
     * Display the specified resource.
     * (อาจจะไม่จำเป็นต้องมีหน้า show แยกสำหรับ Master Data ง่ายๆ สามารถดู/แก้ไขจาก index/edit ได้)
     */
    public function show(Location $location)
    {
        // return view('admin.locations.show', compact('location'));
        return redirect()->route('admin.locations.edit', $location); // Redirect ไปหน้า edit แทน
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Location $location)
    {
        return view('admin.locations.edit', compact('location'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Location $location)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:locations,name,' . $location->id,
            'building' => 'nullable|string|max:255',
            'floor' => 'nullable|string|max:50',
            'room_number' => 'nullable|string|max:50',
            'details' => 'nullable|string',
        ]);

        $location->update($validatedData);

        return redirect()->route('admin.locations.index')->with('status', 'ข้อมูลสถานที่ถูกอัปเดตเรียบร้อยแล้ว');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location)
    {
        // เพิ่มการตรวจสอบก่อนลบ เช่น สถานที่นี้มีการใช้งานใน Repair Requests หรือไม่
        if ($location->repairRequests()->count() > 0) {
            return redirect()->route('admin.locations.index')
                             ->with('error', 'ไม่สามารถลบสถานที่นี้ได้ เนื่องจากมีการใช้งานอยู่ในการแจ้งซ่อม');
        }

        $location->delete();
        return redirect()->route('admin.locations.index')->with('status', 'สถานที่ถูกลบเรียบร้อยแล้ว');
    }
}