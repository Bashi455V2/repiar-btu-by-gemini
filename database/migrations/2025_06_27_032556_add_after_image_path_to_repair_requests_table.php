<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('repair_requests', function (Blueprint $table) {
            // เพิ่มคอลัมน์ after_image_path ต่อจาก image_path เดิม (ถ้ามี)
            // เป็น varchar(255) และสามารถเป็นค่าว่าง (NULL) ได้
            $table->string('after_image_path')->nullable()->after('image_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('repair_requests', function (Blueprint $table) {
            // คำสั่งสำหรับตอนที่ต้องการ rollback migration (ลบคอลัมน์ที่เพิ่มเข้าไป)
            $table->dropColumn('after_image_path');
        });
    }
};
