<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\SellerController;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/verify', [AuthController::class, 'verify']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);

Route::get('/products',          [ProductController::class,  'index']);
Route::get('/products/{id}',     [ProductController::class,  'show']);
Route::get('/providers',         [ProviderController::class, 'index']);
Route::get('/providers/{id}',    [ProviderController::class, 'show']);
Route::get('/messages', [MessageController::class, 'index']);
Route::post('/messages', [MessageController::class, 'store']);

// Seller — protected by Bearer token (role: seller)
Route::get('/seller/shop',              [SellerController::class, 'getShop']);
Route::post('/seller/shop',             [SellerController::class, 'upsertShop']);
Route::post('/seller/products',         [SellerController::class, 'addProduct']);
Route::put('/seller/products/{id}',     [SellerController::class, 'updateProduct']);
Route::delete('/seller/products/{id}',  [SellerController::class, 'deleteProduct']);
Route::get('/seller/messages',          [SellerController::class, 'getMessages']);

Route::get('/my-conversations', [MessageController::class, 'myConversations']);
