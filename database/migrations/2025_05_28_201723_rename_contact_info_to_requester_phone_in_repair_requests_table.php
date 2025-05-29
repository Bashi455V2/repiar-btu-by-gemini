<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   // database/migrations/xxxx_xx_xx_xxxxxx_rename_contact_info_to_requester_phone_in_repair_requests_table.php
public function up(): void
{
    Schema::table('repair_requests', function (Blueprint $table) {
        if (Schema::hasColumn('repair_requests', 'contact_info')) {
            $table->renameColumn('contact_info', 'requester_phone');
        }
    });
}

public function down(): void
{
    Schema::table('repair_requests', function (Blueprint $table) {
        if (Schema::hasColumn('repair_requests', 'requester_phone')) {
            $table->renameColumn('requester_phone', 'contact_info');
        }
    });
}
};
