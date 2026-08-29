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
        Schema::create('payment_type_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_type_id')->constrained('payment_types')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->tinyInteger('class_level')->unsigned()->nullable()->index(); // null = applies to all classes
            $table->string('rombel', 10)->nullable()->index(); // null = applies to all rombels in class
            $table->bigInteger('amount')->unsigned(); // in IDR
            $table->timestamps();

            $table->unique(['payment_type_id', 'academic_year_id', 'class_level', 'rombel'], 'ptp_unique_pricing');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_type_prices');
    }
};
