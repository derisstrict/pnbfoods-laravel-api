<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProdukController;
use App\Http\Controllers\Api\KantinController;

Route::apiResource('produk', ProdukController::class);
Route::apiResource('kantin', KantinController::class);