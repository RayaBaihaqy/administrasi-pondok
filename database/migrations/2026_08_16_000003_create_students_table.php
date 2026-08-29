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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->index()->constrained('parents')->nullOnDelete();
            $table->string('nis', 100)->unique(); // NISN
            $table->string('nism', 50)->nullable(); // NISM
            $table->string('full_name')->index();
            $table->string('gender'); // male, female
            $table->date('birth_date')->nullable();
            $table->tinyInteger('class_level')->unsigned()->index(); // 1-9
            $table->string('rombel', 10)->index(); // A, B
            $table->year('entry_year')->nullable();
            $table->string('status')->default('active')->index(); // active, graduated, withdrawn, inactive
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
