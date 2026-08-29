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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->constrained('bills')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('parents')->nullOnDelete();
            $table->string('payment_number', 100)->unique(); // PAY-YYYYMMDD-XXXX
            $table->bigInteger('amount')->unsigned(); // Nominal transaksi pembayaran
            $table->string('source', 30)->default('midtrans')->index(); // midtrans, manual
            $table->string('method', 50)->nullable()->index(); // qris, bank_transfer, cash, gopay, shopeepay, dll
            $table->string('status', 30)->default('pending')->index(); // pending, success, failed, expired, cancelled
            $table->string('snap_token')->nullable(); // Midtrans Snap Token
            $table->string('transaction_reference', 100)->nullable()->unique(); // Midtrans order_id / transaction_id
            $table->timestamp('paid_at')->nullable()->index();
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete(); // Admin/operator pencatat (manual)
            $table->timestamps();
            $table->softDeletes();

            $table->index(['bill_id', 'status']);
            $table->index(['paid_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
