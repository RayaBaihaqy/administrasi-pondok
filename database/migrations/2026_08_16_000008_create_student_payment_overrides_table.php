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
        Schema::create('student_payment_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->date('disbursed_at')->index(); // Tanggal Diberikan Beasiswa (Tgl/Bln/Thn)
            $table->bigInteger('amount')->unsigned(); // Nominal Beasiswa dalam Rupiah
            $table->text('reason')->nullable(); // Keterangan / Jenis Beasiswa
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_payment_overrides');
    }
};
