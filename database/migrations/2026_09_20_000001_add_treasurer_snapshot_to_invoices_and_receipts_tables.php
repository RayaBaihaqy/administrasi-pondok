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
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('treasurer_name')->nullable()->after('file_path');
            $table->string('treasurer_signature_path')->nullable()->after('treasurer_name');
        });

        Schema::table('receipts', function (Blueprint $table) {
            $table->string('treasurer_name')->nullable()->after('file_path');
            $table->string('treasurer_signature_path')->nullable()->after('treasurer_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['treasurer_name', 'treasurer_signature_path']);
        });

        Schema::table('receipts', function (Blueprint $table) {
            $table->dropColumn(['treasurer_name', 'treasurer_signature_path']);
        });
    }
};
