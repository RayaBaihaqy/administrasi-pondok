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
        Schema::table('bills', function (Blueprint $table) {
            $table->index(['status', 'due_date'], 'idx_bills_status_due_date');
            $table->index(['student_id', 'status'], 'idx_bills_student_status');
            $table->index(['parent_id', 'status'], 'idx_bills_parent_status');
            $table->index('billing_period', 'idx_bills_billing_period');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index(['status', 'paid_at'], 'idx_payments_status_paid_at');
            $table->index(['bill_id', 'status'], 'idx_payments_bill_status');
            $table->index(['student_id', 'status'], 'idx_payments_student_status');
            $table->index(['parent_id', 'status'], 'idx_payments_parent_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->dropIndex('idx_bills_status_due_date');
            $table->dropIndex('idx_bills_student_status');
            $table->dropIndex('idx_bills_parent_status');
            $table->dropIndex('idx_bills_billing_period');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('idx_payments_status_paid_at');
            $table->dropIndex('idx_payments_bill_status');
            $table->dropIndex('idx_payments_student_status');
            $table->dropIndex('idx_payments_parent_status');
        });
    }
};
