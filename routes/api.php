<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProdukController;
use App\Http\Controllers\Api\KantinController;
use App\Http\Controllers\Api\OrderanController;
use App\Http\Controllers\Api\DetailOrderanController;
use App\Http\Controllers\Api\PembayaranController;
use App\Http\Controllers\Api\FavoritController;
use App\Http\Controllers\Api\PelangganController;
use App\Http\Controllers\Api\PenjualController;
use App\Http\Controllers\Api\MidtransController;

Route::get('orderan/pelanggan/{pelangganId}', [OrderanController::class, 'getByPelanggan']);

Route::apiResource('produk', ProdukController::class);
Route::get('kantin/penjual/{penjual_id}', [KantinController::class, 'dariPenjualId']);
Route::apiResource('kantin', KantinController::class);
Route::apiResource('detail-orderan', DetailOrderanController::class);
Route::apiResource('pembayaran', PembayaranController::class);
Route::apiResource('favorit', FavoritController::class);

Route::post('pelanggan/login', [PelangganController::class, 'login']);
Route::post('penjual/login', [PenjualController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('pelanggan', PelangganController::class)->except(['store']);
    Route::post('pelanggan/logout', [PelangganController::class, 'logout']);
    Route::get('orderan/riwayat', [OrderanController::class, 'riwayat']); //?db riwayat || untuk route ny
    Route::apiResource('orderan', OrderanController::class);
});
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('penjual', PenjualController::class)->except(['store']);
    Route::post('penjual/logout', [PenjualController::class, 'logout']);
});
Route::post('pelanggan', [PelangganController::class, 'store']);
Route::post('penjual', [PenjualController::class, 'store']);
Route::post('/pelanggan/forgot-password', [PelangganController::class, 'forgotPassword']);
Route::post('/penjual/forgot-password', [PenjualController::class, 'forgotPassword']);

Route::get('produk/penjual/{penjual_id}', [ProdukController::class, 'dariPenjualId']);

Route::post('pembayaran/snap', [MidtransController::class, 'snapTransaction']);
Route::get('pembayaran/{pembayaran}/status', [MidtransController::class, 'status']);
Route::get('pembayaran/callback', [MidtransController::class, 'callback']);

Route::post('midtrans/notification', [MidtransController::class, 'notification'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
