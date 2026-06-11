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
       Schema::create('kantin', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kantin');
            $table->string('foto_kantin')->nullable();
            $table->string('kategori');
            $table->foreignId('penjual_id')->nullable();
            $table->timestamps();
        });

        Schema::table('kantin', function (Blueprint $table) {
            $table->foreign('penjual_id')->references('id')->on('penjual')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kantin', function (Blueprint $table) {
            $table->dropForeign(['penjual_id']);
        });

        Schema::dropIfExists('kantins');
    }
};
