<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orderan', function (Blueprint $table) {
            $table->id();
            $table->string('status_orderan', 50);
            $table->decimal('total_harga', 15, 2);
            $table->dateTime('tanggal_orderan');
            $table->foreignId('pelanggan_id')->constrained('pelanggan')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orderan');
    }
};
