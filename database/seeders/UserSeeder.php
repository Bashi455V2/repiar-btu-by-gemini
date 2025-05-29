<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User; // <--- Import User Model
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin User
        User::create([
            'name' => 'Admin BtuRepair',
            'email' => 'admin@bturepair.com',
            'password' => Hash::make('password123'), // เปลี่ยนรหัสผ่านตามต้องการ
            'email_verified_at' => now(),
            'is_admin' => true,
            'is_technician' => false,
        ]);

        // Technician User 1
        User::create([
            'name' => 'Technician One',
            'email' => 'tech1@bturepair.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'is_admin' => false,
            'is_technician' => true,
        ]);

        // Technician User 2
        User::create([
            'name' => 'Technician Two',
            'email' => 'tech2@bturepair.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'is_admin' => false,
            'is_technician' => true,
        ]);

        // Normal User (ตัวอย่าง)
        User::create([
            'name' => 'Test User',
            'email' => 'user@bturepair.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'is_admin' => false,
            'is_technician' => false,
        ]);

        // ***** เพิ่ม User ใหม่ตรงนี้ *****
        User::create([
            'name' => 'Normal User Two', // <--- ชื่อใหม่
            'email' => 'user2@bturepair.com', // <--- อีเมลใหม่ (ห้ามซ้ำ)
            'password' => Hash::make('newpassword456'), // <--- รหัสผ่านใหม่
            'email_verified_at' => now(),
            'is_admin' => false, // <--- กำหนด role
            'is_technician' => false, // <--- กำหนด role
        ]);
        // ***** สิ้นสุดการเพิ่ม User ใหม่ *****

    }
}