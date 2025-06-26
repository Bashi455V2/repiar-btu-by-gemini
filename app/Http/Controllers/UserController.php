<?php

namespace App\Http\Controllers; // หรือ App\Http\Controllers\Admin; ถ้าคุณย้ายแล้ว

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;
// use App\Http\Controllers\Controller; // ถ้าไม่ได้ extends Controller หลักโดยตรง

class UserController extends Controller // ตรวจสอบว่า extends Controller หลัก
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::paginate(10);
        // ถ้า View ของคุณอยู่ที่ resources/views/admin/users/index.blade.php
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // ถ้า View ของคุณอยู่ที่ resources/views/admin/users/create.blade.php
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'is_admin' => ['nullable','boolean'], // เปลี่ยนเป็น nullable ถ้าคุณส่ง 0 เมื่อไม่ check
            'is_technician' => ['nullable','boolean'], // เปลี่ยนเป็น nullable
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => $request->boolean('is_admin'), // ใช้ boolean() helper
            'is_technician' => $request->boolean('is_technician'),
        ]);

        return redirect()->route('admin.users.index')->with('status', 'ผู้ใช้ถูกเพิ่มเรียบร้อยแล้ว'); // <--- **แก้ไขตรงนี้**
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        // ถ้า View ของคุณอยู่ที่ resources/views/admin/users/edit.blade.php
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'is_admin' => ['nullable','boolean'], // เปลี่ยนเป็น nullable
            'is_technician' => ['nullable','boolean'], // เปลี่ยนเป็น nullable
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->is_admin = $request->boolean('is_admin');
        $user->is_technician = $request->boolean('is_technician');
        $user->save();

        return redirect()->route('admin.users.index')->with('status', 'ข้อมูลผู้ใช้ถูกอัปเดตเรียบร้อยแล้ว'); // <--- **แก้ไขตรงนี้**
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // เพิ่มการตรวจสอบสิทธิ์ก่อนลบ (เช่น ไม่ให้ลบตัวเอง หรือ Admin คนสุดท้าย)
        if (Auth::id() === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'ไม่สามารถลบบัญชีผู้ใช้ของตัวเองได้');
        }
        // อาจจะมีเงื่อนไขเพิ่มเติม เช่น ไม่ให้ลบ Admin คนอื่นถ้าไม่ใช่ Super Admin

        $user->delete();
        return redirect()->route('admin.users.index')->with('status', 'ผู้ใช้ถูกลบเรียบร้อยแล้ว'); // <--- **แก้ไขตรงนี้**
    }
}