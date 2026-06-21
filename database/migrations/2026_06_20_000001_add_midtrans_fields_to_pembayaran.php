<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->string('snap_token')->nullable()->after('status_pembayaran');
            $table->string('snap_redirect_url')->nullable()->after('snap_token');
            $table->string('midtrans_order_id', 50)->nullable()->unique()->after('snap_redirect_url');
            $table->string('midtrans_transaction_id')->nullable()->after('midtrans_order_id');
            $table->string('midtrans_transaction_status', 30)->nullable()->after('midtrans_transaction_id');
            $table->json('midtrans_response')->nullable()->after('midtrans_transaction_status');
            $table->dateTime('expired_at')->nullable()->after('midtrans_response');
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropColumn([
                'snap_token',
                'snap_redirect_url',
                'midtrans_order_id',
                'midtrans_transaction_id',
                'midtrans_transaction_status',
                'midtrans_response',
                'expired_at',
            ]);
        });
    }
};
