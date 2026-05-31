<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProdukController;

Route::apiResource('produk', ProdukController::class);
