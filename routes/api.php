<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// RSO OCR Route
Route::post('/rso/ocr', [App\Http\Controllers\RsoController::class, 'extractSubject']);

// RSO API Resource
Route::apiResource('rso', App\Http\Controllers\RsoController::class);

// User Route
Route::get('user', [App\Http\Controllers\RsoController::class, 'user']);
