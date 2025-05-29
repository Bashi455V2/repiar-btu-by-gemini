<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category; // <--- Import Category Model

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create(['name' => 'ไฟฟ้า', 'description' => 'ปัญหาเกี่ยวกับระบบไฟฟ้า หลอดไฟ ปลั๊กไฟ']);
        Category::create(['name' => 'ประปา', 'description' => 'ปัญหาเกี่ยวกับระบบน้ำ ท่อตัน ก๊อกน้ำรั่ว']);
        Category::create(['name' => 'เครื่องปรับอากาศ', 'description' => 'แอร์ไม่เย็น แอร์มีเสียงดัง']);
        Category::create(['name' => 'อุปกรณ์คอมพิวเตอร์', 'description' => 'คอมพิวเตอร์ โปรเจคเตอร์ ปริ้นเตอร์']);
        Category::create(['name' => 'อาคารสถานที่', 'description' => 'ประตู หน้าต่าง ผนัง พื้น เพดาน']);
        Category::create(['name' => 'อื่นๆ', 'description' => 'ปัญหาอื่นๆ ที่ไม่เข้าพวก']);
        // เพิ่มหมวดหมู่อื่นๆ ตามต้องการ
    }
}