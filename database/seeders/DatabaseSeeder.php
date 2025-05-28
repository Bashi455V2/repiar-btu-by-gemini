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
        // User::factory(10)->create(); // ถ้ามีอยู่แล้ว อาจจะ comment หรือลบทิ้งไปก่อน

        $this->call([
            UserSeeder::class, // เพิ่มบรรทัดนี้
        ]);
    }
}