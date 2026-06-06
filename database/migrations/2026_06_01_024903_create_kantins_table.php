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
            $table->timestamps();
        });

        Schema::table('penjual', function (Blueprint $table) {
            $table->foreign('kantin_id')->references('id')->on('kantin')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penjual', function (Blueprint $table) {
            $table->dropForeign(['kantin_id']);
        });

        Schema::dropIfExists('kantins');
    }
};
