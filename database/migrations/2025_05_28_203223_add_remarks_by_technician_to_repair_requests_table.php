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
            // เพิ่มคอลัมน์ remarks_by_technician (สามารถเป็น text และ nullable)
            // คุณสามารถใส่ after() เพื่อกำหนดตำแหน่งคอลัมน์ได้ถ้าต้องการ
            $table->text('remarks_by_technician')->nullable()->after('assigned_to_user_id'); // หรือ after('assigned_to') ถ้าคุณยังใช้ชื่อนั้น
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('repair_requests', function (Blueprint $table) {
            if (Schema::hasColumn('repair_requests', 'remarks_by_technician')) {
                $table->dropColumn('remarks_by_technician');
            }
        });
    }
};