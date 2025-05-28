<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
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
            'is_admin' => ['boolean'], // Validate as boolean (0 or 1 from hidden input)
            'is_technician' => ['boolean'], // Validate as boolean
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            // แก้ไขตรงนี้: ใช้ input() และแปลงเป็น boolean
            'is_admin' => (bool) $request->input('is_admin'),
            'is_technician' => (bool) $request->input('is_technician'),
        ]);

        return redirect()->route('users.index')->with('status', 'ผู้ใช้ถูกเพิ่มเรียบร้อยแล้ว');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        // ในที่นี้เราจะไม่มีหน้า show แยก แต่จะจัดการผ่าน index, create, edit
        return redirect()->route('users.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
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
            'is_admin' => ['boolean'], // Validate as boolean
            'is_technician' => ['boolean'], // Validate as boolean
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        // ถ้ามีการกรอกรหัสผ่านใหม่
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // แก้ไขตรงนี้: ใช้ input() และแปลงเป็น boolean
        $user->is_admin = (bool) $request->input('is_admin');
        $user->is_technician = (bool) $request->input('is_technician');

        $user->save();

        return redirect()->route('users.index')->with('status', 'ข้อมูลผู้ใช้ถูกอัปเดตเรียบร้อยแล้ว');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('status', 'ผู้ใช้ถูกลบเรียบร้อยแล้ว');
    }
}