<?php

namespace App\Http\Controllers\Admin; // <--- Namespace Admin

use App\Http\Controllers\Controller;
use App\Models\Category; // <--- Import Category Model
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::orderBy('name')->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
        ]);

        Category::create($validatedData);

        return redirect()->route('admin.categories.index')->with('status', 'หมวดหมู่ถูกเพิ่มเรียบร้อยแล้ว');
    }

    /**
     * Display the specified resource.
     * (สำหรับ Master Data ง่ายๆ อาจจะไม่จำเป็นต้องมีหน้า Show แยก)
     */
    public function show(Category $category)
    {
        return redirect()->route('admin.categories.edit', $category); // Redirect ไปหน้า edit แทน
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
        ]);

        $category->update($validatedData);

        return redirect()->route('admin.categories.index')->with('status', 'ข้อมูลหมวดหมู่ถูกอัปเดตเรียบร้อยแล้ว');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // เพิ่มการตรวจสอบก่อนลบ: หมวดหมู่นี้มีการใช้งานใน Repair Requests หรือไม่
        if ($category->repairRequests()->count() > 0) {
            return redirect()->route('admin.categories.index')
                             ->with('error', 'ไม่สามารถลบหมวดหมู่นี้ได้ เนื่องจากมีการใช้งานอยู่ในการแจ้งซ่อม');
        }

        $category->delete();
        return redirect()->route('admin.categories.index')->with('status', 'หมวดหมู่ถูกลบเรียบร้อยแล้ว');
    }
}