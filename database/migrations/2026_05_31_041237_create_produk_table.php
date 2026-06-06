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
        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->string('nama_produk');
            $table->string('foto_produk')->nullable();
            $table->text('deskripsi_produk')->nullable();
            $table->string('kategori_produk', 100);
            $table->decimal('harga_produk', 15, 2);
            $table->foreignId('penjual_id')->constrained('penjual')->cascadeOnDelete();
            $table->integer('stok')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
