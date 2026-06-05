<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProdukController;
use App\Http\Controllers\Api\KantinController;
use App\Http\Controllers\Api\PelangganController;
use App\Http\Controllers\Api\PenjualController;

Route::apiResource('produk', ProdukController::class);
Route::apiResource('kantin', KantinController::class);
Route::post('pelanggan/login', [PelangganController::class, 'login']);
Route::post('penjual/login', [PenjualController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('pelanggan', PelangganController::class)->except(['store']);
    Route::post('pelanggan/logout', [PelangganController::class, 'logout']);
});
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('penjual', PenjualController::class)->except(['store']);
    Route::post('penjual/logout', [PenjualController::class, 'logout']);
});
Route::post('pelanggan', [PelangganController::class, 'store']);
Route::post('penjual', [PenjualController::class, 'store']);