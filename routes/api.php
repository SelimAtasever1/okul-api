<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PreorderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/preorders', [PreorderController::class, 'createPreorder']);

Route::post('/cart/store-logged-user-cart', [CartController::class, 'storeAsLoggedUser']);
Route::post('/cart/store-guest-cart', [CartController::class, 'storeAsGuest']);
Route::patch('/cart/update', [CartController::class, 'update']);  
Route::delete('/cart/delete', [CartController::class, 'destroy']);

Route::middleware(['auth.basic'])->group(function () {
    Route::put('/preorders/{id}/approve', [PreorderController::class, 'approve']);
    Route::get('/preorders', [PreorderController::class, 'index']);
    Route::get('/preorders/{id}', [PreorderController::class, 'show']);
});

