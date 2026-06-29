<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\MessageController;

Route::get('/products',          [ProductController::class,  'index']);
Route::get('/products/{id}',     [ProductController::class,  'show']);
Route::get('/providers',         [ProviderController::class, 'index']);
Route::get('/providers/{id}',    [ProviderController::class, 'show']);
Route::get('/messages', [MessageController::class, 'index']);
Route::post('/messages', [MessageController::class, 'store']);
