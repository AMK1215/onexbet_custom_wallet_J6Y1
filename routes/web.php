<?php

use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\SubAccountController;
use App\Http\Controllers\TelegramBotController;
use App\Http\Controllers\TransferLogController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

// Auth routes (for admin login)
Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Password change routes
Route::get('get-change-password', [LoginController::class, 'changePassword'])->name('getChangePassword');
Route::post('update-password/{user}', [LoginController::class, 'updatePassword'])->name('updatePassword');

// Public routes (for frontend/player access)
Route::get('/', [HomeController::class, 'index'])->name('public.home');
Route::get('/profile', [HomeController::class, 'profile'])->name('profile');

// Include admin routes
require_once __DIR__.'/admin.php';
Route::get('admin/product/game-list', [\App\Http\Controllers\Admin\ProductController::class, 'GameListFetch'])->name('admin.product.game-list');

