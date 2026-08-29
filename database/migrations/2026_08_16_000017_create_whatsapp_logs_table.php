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
        Schema::create('whatsapp_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('parents')->onDelete('set null');
            $table->foreignId('student_id')->nullable()->constrained('students')->onDelete('set null');
            $table->string('recipient_name');
            $table->string('phone_number');
            $table->string('message_type'); // pembayaran_sukses, pengingat_tagihan, tagihan_baru, tagihan_menunggak
            $table->unsignedBigInteger('amount')->nullable();
            $table->text('message_content');
            $table->string('status')->default('terkirim'); // terkirim, pending, gagal
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_logs');
    }
};
