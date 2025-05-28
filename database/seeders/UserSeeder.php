<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ผู้ใช้ Admin หลัก (เป็นทั้ง Admin และ Technician เพื่อแก้ปัญหาเฉพาะหน้าใน Laravel 12.16.0)
        User::create([
            'name' => 'Primary Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'), // ตั้งรหัสผ่านที่คุณต้องการ
            'is_admin' => true,
            'is_technician' => false, // ทำให้เป็น Technician ด้วย เพื่อให้ผ่าน admin_or_technician middleware
        ]);

        // ผู้ใช้ Admin คนที่สอง (เป็นทั้ง Admin และ Technician)
        // เพื่อทดสอบว่าปัญหาเฉพาะผู้ใช้เดิมหรือไม่
        User::create([
            'name' => 'Secondary Admin',
            'email' => 'admin2@example.com', // ใช้อีเมลที่ไม่ซ้ำ
            'password' => Hash::make('password123'), // ตั้งรหัสผ่านที่คุณต้องการ
            'is_admin' => true,
            'is_technician' => true, // ทำให้เป็น Technician ด้วย
        ]);

        // ผู้ใช้ Technician
        User::create([
            'name' => 'Technician User',
            'email' => 'tech@example.com',
            'password' => Hash::make('techpass'),
            'is_admin' => true,
            'is_technician' => true,
        ]);

        // ผู้ใช้ทั่วไป
        User::create([
            'name' => 'Normal User',
            'email' => 'user@example.com',
            'password' => Hash::make('userpass'),
            'is_admin' => false,
            'is_technician' => false,
        ]);
    }
}

