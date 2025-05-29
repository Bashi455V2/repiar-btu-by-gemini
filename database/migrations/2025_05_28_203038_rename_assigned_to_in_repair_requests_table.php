<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   // database/migrations/xxxx_xx_xx_xxxxxx_rename_assigned_to_in_repair_requests_table.php
public function up(): void
{
    Schema::table('repair_requests', function (Blueprint $table) {
        if (Schema::hasColumn('repair_requests', 'assigned_to')) {
            // ก่อน rename อาจจะต้อง drop foreign key constraint เดิมก่อน
            // Laravel จะตั้งชื่อ constraint อัตโนมัติ เช่น repair_requests_assigned_to_foreign
            // คุณอาจจะต้องหาชื่อ constraint ที่ถูกต้องจากฐานข้อมูลของคุณ
            // $table->dropForeign(['assigned_to']); // หรือ $table->dropForeign('ชื่อconstraintเดิม');

            $table->renameColumn('assigned_to', 'assigned_to_user_id');

            // แล้วสร้าง foreign key constraint ใหม่สำหรับ assigned_to_user_id (ถ้า drop ไป)
            // $table->foreign('assigned_to_user_id')->references('id')->on('users')->onDelete('set null');
        }
    });
}

public function down(): void
{
    Schema::table('repair_requests', function (Blueprint $table) {
        if (Schema::hasColumn('repair_requests', 'assigned_to_user_id')) {
            // $table->dropForeign(['assigned_to_user_id']);
            $table->renameColumn('assigned_to_user_id', 'assigned_to');
            // $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
        }
    });
}
};
