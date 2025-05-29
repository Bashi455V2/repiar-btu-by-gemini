<?php

// database/migrations/xxxx_xx_xx_xxxxxx_modify_repair_requests_for_foreign_keys.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('repair_requests', function (Blueprint $table) {
            // 1. ลบคอลัมน์ string เดิม
            if (Schema::hasColumn('repair_requests', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('repair_requests', 'location')) {
                $table->dropColumn('location');
            }
            if (Schema::hasColumn('repair_requests', 'priority')) {
                $table->dropColumn('priority');
            }

            // 2. เพิ่มคอลัมน์ Foreign Key ใหม่สำหรับ status, location, category
            $table->foreignId('status_id')->after('description')->nullable()->constrained('statuses')->comment('FK to statuses table');
            $table->foreignId('location_id')->after('status_id')->nullable()->constrained('locations')->onDelete('set null')->comment('FK to locations table');
            $table->foreignId('category_id')->after('location_id')->nullable()->constrained('categories')->onDelete('set null')->comment('FK to categories table');

            // 3. ส่วนของ assigned_to และ user_id ไม่ต้องทำอะไรเพิ่มแล้ว
            // เพราะ FK constraint ได้ถูกสร้างไว้ตั้งแต่ใน create_repair_requests_table.php
            // ถ้าต้องการเปลี่ยน onDelete action หรือ properties อื่นๆ ของคอลัมน์ (ที่ไม่ใช่การเพิ่ม FK ซ้ำ)
            // คุณอาจจะต้อง drop foreign key เดิมก่อน แล้วค่อย add ใหม่ด้วย option ที่ต้องการ
            // แต่จากโค้ดปัจจุบัน ไม่จำเป็นต้องทำอะไรกับ user_id และ assigned_to ในไฟล์นี้อีก

            // ตัวอย่าง ถ้าต้องการเปลี่ยน onDelete action ของ user_id (สมมติว่าเดิมเป็น cascade แล้วอยากเปลี่ยนเป็น set null)
            // if (Schema::hasColumn('repair_requests', 'user_id')) {
            //     $table->dropForeign(['user_id']); // ลบ FK เดิมก่อน
            //     // $table->unsignedBigInteger('user_id')->nullable()->change(); // ถ้าจำเป็นต้องเปลี่ยน nullability
            //     $table->foreign('user_id')->references('id')->on('users')->onDelete('set null')->change(); // เพิ่ม FK ใหม่พร้อม onDelete action ที่ต้องการ
            // }
            // ทำนองเดียวกันสำหรับ assigned_to ถ้าต้องการแก้ไข constraint ที่มีอยู่
        });
    }

    public function down(): void
    {
        Schema::table('repair_requests', function (Blueprint $table) {
            // ลบ Foreign Key Constraints และคอลัมน์ที่เพิ่มใน up() นี้เท่านั้น
            $table->dropForeign(['status_id']);
            $table->dropForeign(['location_id']);
            $table->dropForeign(['category_id']);

            $table->dropColumn(['status_id', 'location_id', 'category_id']);

            // เพิ่มคอลัมน์ string เดิมกลับเข้ามา
            $table->string('status')->default('pending')->after('description');
            $table->string('location')->after('status');
            $table->string('priority')->default('normal')->after('location');

            // ไม่ต้องยุ่งกับ user_id และ assigned_to ใน down() นี้
            // เพราะเราไม่ได้แก้ไข FK ของมันใน up() ของไฟล์นี้
        });
    }
};