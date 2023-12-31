<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InfoController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\GreetController;
use App\Http\Controllers\SendEmailController;
use App\Http\Controllers\Auth\LoginRegisterController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', function () {
    return view('login');
});

Route::get('/send-mail',[SendEmailController::class,
'index'])->name('Kiri-email');
Route::post('/post-email', [SendEmailController::class, 'store'])->name('post-email');

Route::controller(LoginRegisterController::class)->group(function() {
    Route::get('/register', 'register')->name('register');
    Route::post('/store', 'store')->name('store');
    Route::get('/login', 'login')->name('login');
    Route::post('/authenticate', 'authenticate')->name('authenticate');
    Route::get('/dashboard', 'dashboard')->name('dashboard')->middleware('auth');
    Route::post('/logout', 'logout')->name('logout');
});

Route::resource('posts', PostController::class);
Route::get('/dashboard2', [PostController::class, 'dashboard2'])-> name('dashboard2');

Route::get('/info', [InfoController::class, 'index'])->name('info');
// Route::get('/greet', [GreetController::class, 'greet'])->name('greet');
