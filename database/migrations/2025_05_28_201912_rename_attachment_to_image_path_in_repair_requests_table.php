<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   // database/migrations/xxxx_xx_xx_xxxxxx_rename_attachment_to_image_path_in_repair_requests_table.php
public function up(): void
{
    Schema::table('repair_requests', function (Blueprint $table) {
        if (Schema::hasColumn('repair_requests', 'attachment')) {
            $table->renameColumn('attachment', 'image_path');
        }
    });
}

public function down(): void
{
    Schema::table('repair_requests', function (Blueprint $table) {
        if (Schema::hasColumn('repair_requests', 'image_path')) {
            $table->renameColumn('image_path', 'attachment');
        }
    });
}
};
