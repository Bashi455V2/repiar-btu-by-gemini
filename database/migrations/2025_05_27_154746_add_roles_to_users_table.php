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
        Schema::table('users', function (Blueprint $table) {
            // เพิ่มคอลัมน์ is_admin และ is_technician เป็น boolean และมีค่าเริ่มต้นเป็น false
            $table->boolean('is_admin')->default(false)->after('email'); // เพิ่มหลังคอลัมน์ email
            $table->boolean('is_technician')->default(false)->after('is_admin'); // เพิ่มหลังคอลัมน์ is_admin
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // คำสั่งสำหรับย้อนกลับ (ลบคอลัมน์)
            $table->dropColumn(['is_admin', 'is_technician']);
        });
    }
};