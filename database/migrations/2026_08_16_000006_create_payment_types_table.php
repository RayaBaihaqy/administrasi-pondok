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
        Schema::create('payment_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->string('billing_type', 50)->default('monthly'); // monthly, one_time, custom
            $table->boolean('allows_installment')->default(false);
            $table->bigInteger('default_amount')->unsigned()->default(0); // in IDR
            $table->tinyInteger('default_due_day')->unsigned()->default(1); // 1-31 (e.g. tgl 1 tiap bulan)
            $table->boolean('is_active')->default(true)->index();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_types');
    }
};
