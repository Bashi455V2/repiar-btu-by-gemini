<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create(); // ถ้าคุณใช้ Factory

        $this->call([
            UserSeeder::class,
            StatusSeeder::class,
            LocationSeeder::class,
            CategorySeeder::class,
            // RepairRequestSeeder::class, // คุณอาจจะสร้าง Seeder สำหรับ RepairRequest ตัวอย่างทีหลัง
        ]);
    }
}