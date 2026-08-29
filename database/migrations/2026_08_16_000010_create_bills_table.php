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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('parents')->nullOnDelete(); // snapshot parent
            $table->foreignId('payment_type_id')->constrained('payment_types')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->string('bill_number', 100)->unique(); // INV-NIS-YYYYMM-XXXX
            $table->date('billing_period')->index(); // Tanggal/periode penagihan (e.g. 2026-08-01)
            $table->date('billing_date'); // Tanggal diterbitkan
            $table->date('due_date')->index(); // Tanggal jatuh tempo
            $table->bigInteger('amount')->unsigned(); // Total nominal tagihan dalam Rp
            $table->bigInteger('paid_amount')->unsigned()->default(0); // Nominal yang sudah dibayar
            $table->bigInteger('outstanding_amount')->unsigned(); // Sisa tagihan (amount - paid_amount)
            $table->string('status', 30)->default('unpaid')->index(); // unpaid, paid, overdue, cancelled
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Index composite untuk reporting & uniqueness recurring bill
            $table->unique(['student_id', 'payment_type_id', 'academic_year_id', 'billing_period'], 'bills_student_period_unique');
            $table->index(['academic_year_id', 'payment_type_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
