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
        Schema::create('repair_requests', function (Blueprint $table) {
            $table->id(); // Primary Key, Auto-increment
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ผู้แจ้งซ่อม (เชื่อมกับตาราง users)
            $table->string('subject'); // หัวข้อ/อาการเบื้องต้น
            $table->text('description'); // รายละเอียดปัญหา
            $table->string('location'); // สถานที่เกิดเหตุ (เช่น ห้อง, อาคาร, ชั้น)
            $table->string('contact_info')->nullable(); // ข้อมูลติดต่อเพิ่มเติม (เบอร์โทร/อีเมล ถ้า user ไม่ได้ระบุไว้ในระบบ)
            $table->string('status')->default('pending'); // สถานะ: pending, in_progress, completed, cancelled
            $table->string('priority')->default('normal'); // ระดับความสำคัญ: low, normal, high, urgent
            $table->string('attachment')->nullable(); // ชื่อไฟล์รูปภาพ หรือไฟล์แนบ
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null'); // ช่างที่ได้รับมอบหมาย
            $table->timestamp('completed_at')->nullable(); // วันที่ซ่อมเสร็จ
            $table->timestamps(); // created_at และ updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repair_requests');
    }
};