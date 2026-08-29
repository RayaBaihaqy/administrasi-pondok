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
        Schema::create('payment_gateway_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->onDelete('cascade');
            $table->string('provider', 50)->default('midtrans');
            $table->string('order_id', 100)->index();
            $table->string('transaction_id', 100)->nullable()->index();
            $table->string('transaction_status', 50)->index();
            $table->bigInteger('gross_amount')->unsigned();
            $table->string('payment_type', 50)->nullable();
            $table->string('signature_key', 255)->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_gateway_transactions');
    }
};
