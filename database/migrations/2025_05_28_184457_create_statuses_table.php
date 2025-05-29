<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   // database/migrations/xxxx_xx_xx_xxxxxx_create_statuses_table.php
public function up(): void
{
    Schema::create('statuses', function (Blueprint $table) {
        $table->id();
        $table->string('name')->unique(); // เช่น รอดำเนินการ, กำลังดำเนินการ, ซ่อมเสร็จสิ้น, ยกเลิก
        $table->string('color_class')->nullable(); // สำหรับ UI เช่น 'bg-yellow-100 text-yellow-800'
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statuses');
    }
};
