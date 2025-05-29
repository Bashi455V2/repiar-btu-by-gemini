<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Location; // <--- Import Location Model

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Location::create(['name' => 'ตึก A - ห้อง 101', 'building' => 'ตึก A', 'floor' => '1', 'room_number' => '101']);
        Location::create(['name' => 'ตึก A - ห้อง 102', 'building' => 'ตึก A', 'floor' => '1', 'room_number' => '102']);
        Location::create(['name' => 'ตึก B - ห้องปฏิบัติการคอมพิวเตอร์ 1', 'building' => 'ตึก B', 'floor' => '2', 'details' => 'ห้อง Lab Com 1']);
        Location::create(['name' => 'ห้องสมุดกลาง', 'building' => 'อาคารเรียนรวม', 'floor' => '3']);
        Location::create(['name' => 'โรงอาหารกลาง', 'building' => 'ส่วนกลาง']);
        // เพิ่มสถานที่อื่นๆ ตามต้องการ
    }
}