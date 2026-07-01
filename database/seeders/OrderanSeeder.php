<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class OrderanSeeder extends Seeder
{
    public function run(): void
    {
        $pelangganIds = DB::table('pelanggan')->pluck('id');
    $produkIds = DB::table('produk')
            ->join('penjual', 'produk.penjual_id', '=', 'penjual.id')
            ->join('kantin', 'kantin.penjual_id', '=', 'penjual.id')
            ->pluck('produk.id');

    $now = Carbon::now();

    DB::statement('PRAGMA foreign_keys = OFF');
    DB::table('orderan')->truncate();
    DB::table('pembayaran')->truncate();
    if (Schema::hasTable('detail_orderan')) {
        DB::table('detail_orderan')->truncate();
    }

    $dataset = [
        [
            'status_orderan'    => 'lunas',
            'tanggal_orderan'   => $now->copy()->subDays(1)->setTime(12, 38, 00),
            'pelanggan_id'      => $pelangganIds->first(),
            'status_pembayaran' => 'Berhasil',
            'snap_token'        => 'snap-token-001-xyz',
            'midtrans_status'   => 'settlement',
            'items' => [
                ['produk_id' => $produkIds->get(0) ?? $produkIds->first(), 'jumlah' => 2],
                ['produk_id' => $produkIds->get(1) ?? $produkIds->first(), 'jumlah' => 1],
            ]
        ],
        [
            'status_orderan'    => 'diproses',
            'tanggal_orderan'   => $now->copy()->subDays(1)->setTime(12, 38, 00),
            'pelanggan_id'      => $pelangganIds->first(),
            'status_pembayaran' => 'Berhasil',
            'snap_token'        => 'snap-token-001-xyz',
            'midtrans_status'   => 'settlement',
            'items' => [
                ['produk_id' => $produkIds->get(0) ?? $produkIds->first(), 'jumlah' => 3],
                ['produk_id' => $produkIds->get(1) ?? $produkIds->first(), 'jumlah' => 1],
            ]
        ],
        [
            'status_orderan'    => 'menunggu',
            'tanggal_orderan'   => $now->copy()->subDays(1)->setTime(12, 38, 00),
            'pelanggan_id'      => $pelangganIds->first(),
            'status_pembayaran' => 'Berhasil',
            'snap_token'        => 'snap-token-001-xyz',
            'midtrans_status'   => 'settlement',
            'items' => [
                ['produk_id' => $produkIds->get(0) ?? $produkIds->first(), 'jumlah' => 2],
                ['produk_id' => $produkIds->get(1) ?? $produkIds->first(), 'jumlah' => 10],
            ]
        ],
        [
            'status_orderan'    => 'batal',
            'tanggal_orderan'   => $now->copy()->subDays(1)->setTime(12, 38, 00),
            'pelanggan_id'      => $pelangganIds->first(),
            'status_pembayaran' => 'Gagal',
            'snap_token'        => 'snap-token-001-xyz',
            'midtrans_status'   => 'settlement',
            'items' => [
                ['produk_id' => $produkIds->get(0) ?? $produkIds->first(), 'jumlah' => 3],
                ['produk_id' => $produkIds->get(1) ?? $produkIds->first(), 'jumlah' => 1],
            ]
        ],
        [
            'status_orderan'    => 'selesai',
            'tanggal_orderan'   => $now->copy()->subDays(1)->setTime(12, 38, 00),
            'pelanggan_id'      => $pelangganIds->first(),
            'status_pembayaran' => 'Berhasil',
            'snap_token'        => 'snap-token-001-xyz',
            'midtrans_status'   => 'settlement',
            'items' => [
                ['produk_id' => $produkIds->get(0) ?? $produkIds->first(), 'jumlah' => 3],
                ['produk_id' => $produkIds->get(1) ?? $produkIds->first(), 'jumlah' => 1],
            ]
        ],
    ];

    foreach ($dataset as $data) {

        // Hitung total harga dinamis
        $totalHarga = 0;
        foreach ($data['items'] as $item) {
            $harga = DB::table('produk')
                ->where('id', $item['produk_id'])
                ->value('harga_produk');

            $totalHarga += $harga * $item['jumlah'];
        }

        // Insert orderan
        $orderanId = DB::table('orderan')->insertGetId([
            'status_orderan'  => $data['status_orderan'],
            'total_harga'     => $totalHarga,
            'tanggal_orderan' => $data['tanggal_orderan'],
            'pelanggan_id'    => $data['pelanggan_id'],
            'created_at'      => $data['tanggal_orderan'],
            'updated_at'      => $data['tanggal_orderan'],
        ]);

        // Insert detail orderan
        if (Schema::hasTable('detail_orderan')) {
            foreach ($data['items'] as $item) {
                DB::table('detail_orderan')->insert([
                    'orderan_id' => $orderanId,
                    'produk_id'  => $item['produk_id'],
                    'jumlah'     => $item['jumlah'],
                    'created_at' => $data['tanggal_orderan'],
                    'updated_at' => $data['tanggal_orderan'],
                ]);
            }
        }

        // Insert pembayaran
        DB::table('pembayaran')->insert([
            'orderan_id'                  => $orderanId,
            'metode_pembayaran'           => 'QRIS',
            'total_pembayaran'            => (float) $totalHarga,  // ← sama
            'status_pembayaran'           => $data['status_pembayaran'],
            'snap_token'                  => $data['snap_token'],
            'snap_redirect_url'           => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/' . $data['snap_token'],
            'qr_image_url'                => 'https://api.sandbox.midtrans.com/v2/qris/' . $data['snap_token'] . '/qr-code',
            'midtrans_transaction_status' => $data['midtrans_status'],
            'expired_at'                  => Carbon::parse($data['tanggal_orderan'])->addHours(2),
            'created_at'                  => $data['tanggal_orderan'],
            'updated_at'                  => $data['tanggal_orderan'],
        ]);
    }

    DB::statement('PRAGMA foreign_keys = ON');

    }
}