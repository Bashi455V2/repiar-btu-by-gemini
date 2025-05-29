<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Status; // <--- Import Status Model

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Status::create(['name' => 'รอดำเนินการ', 'color_class' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100']);
        Status::create(['name' => 'กำลังดำเนินการ', 'color_class' => 'bg-sky-100 text-sky-800 dark:bg-sky-700 dark:text-sky-100']);
        Status::create(['name' => 'ซ่อมเสร็จสิ้น', 'color_class' => 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100']);
        Status::create(['name' => 'ยกเลิก', 'color_class' => 'bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100']);
        Status::create(['name' => 'รออะไหล่', 'color_class' => 'bg-orange-100 text-orange-800 dark:bg-orange-700 dark:text-orange-100']);
        // เพิ่มสถานะอื่นๆ ตามต้องการ
    }
}