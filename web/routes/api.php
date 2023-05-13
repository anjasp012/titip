<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIController;

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

Route::post('profile', [APIController::class, 'profile']);
Route::post('products', [APIController::class, 'products']);
Route::post('order', [APIController::class, 'order']);
Route::post('status', [APIController::class, 'status']);
Route::get('profile', function () {
    return response()->json([
        'status'  => false,
        'data'    => [
            'message' => 'Permintaan tidak sesuai.'
        ]
    ], 200, [], JSON_PRETTY_PRINT);
});
Route::get('products', function () {
    return response()->json([
        'status'  => false,
        'data'    => [
            'message' => 'Permintaan tidak sesuai.'
        ]
    ], 200, [], JSON_PRETTY_PRINT);
});
Route::get('order', function () {
    return response()->json([
        'status'  => false,
        'data'    => [
            'message' => 'Permintaan tidak sesuai.'
        ]
    ], 200, [], JSON_PRETTY_PRINT);
});
Route::get('status', function () {
    return response()->json([
        'status'  => false,
        'data'    => [
            'message' => 'Permintaan tidak sesuai.'
        ]
    ], 200, [], JSON_PRETTY_PRINT);
});
