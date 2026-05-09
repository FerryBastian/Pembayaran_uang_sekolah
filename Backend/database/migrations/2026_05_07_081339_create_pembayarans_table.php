<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tagihan_siswa_id')->unique()->constrained('tagihan_siswas')->cascadeOnDelete();
            $table->string('order_id', 100)->unique();
            $table->decimal('gross_amount', 12, 2);
            $table->string('payment_type', 50)->nullable();
            $table->string('transaction_status', 50)->default('pending');
            $table->datetime('transaction_time')->nullable();
            $table->text('snap_token');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
