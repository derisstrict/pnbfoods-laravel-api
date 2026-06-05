<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProdukController;
use App\Http\Controllers\Api\KantinController;
use App\Http\Controllers\Api\OrderanController;
use App\Http\Controllers\Api\DetailOrderanController;
use App\Http\Controllers\Api\PembayaranController;
use App\Http\Controllers\Api\FavoritController;

Route::apiResource('produk', ProdukController::class);
Route::apiResource('kantin', KantinController::class);
Route::apiResource('orderan', OrderanController::class);
Route::apiResource('detail-orderan', DetailOrderanController::class);
Route::apiResource('pembayaran', PembayaranController::class);
Route::apiResource('favorit', FavoritController::class);
