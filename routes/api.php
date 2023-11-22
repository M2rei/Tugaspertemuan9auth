<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GreetController;
use App\Http\Controllers\Api\posttController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/greet', [GreetController::class, 'greet'])->name('greet');
Route::get('/gallery', [posttController::class, 'index'])->name('gallery');
Route::post('/gallery/store', [posttController::class, 'store'])->name('store');
