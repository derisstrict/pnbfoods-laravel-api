<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orderan_id')->unique()->constrained('orderan')->cascadeOnDelete();
            $table->string('metode_pembayaran', 100);
            $table->decimal('total_pembayaran', 15, 2);
            $table->string('status_pembayaran', 50);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
