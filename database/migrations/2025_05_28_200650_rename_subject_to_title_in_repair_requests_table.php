<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  // database/migrations/xxxx_xx_xx_xxxxxx_rename_subject_to_title_in_repair_requests_table.php
public function up(): void
{
    Schema::table('repair_requests', function (Blueprint $table) {
        if (Schema::hasColumn('repair_requests', 'subject')) { // ตรวจสอบว่ามีคอลัมน์ subject อยู่จริง
            $table->renameColumn('subject', 'title');
        }
    });
}

public function down(): void
{
    Schema::table('repair_requests', function (Blueprint $table) {
        if (Schema::hasColumn('repair_requests', 'title')) { // ตรวจสอบว่ามีคอลัมน์ title อยู่จริง (เผื่อ rollback)
            $table->renameColumn('title', 'subject');
        }
    });
}
};
