<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HabitController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\StatsController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/', DashboardController::class)->name('home');

    Route::get('habits', [HabitController::class, 'index'])->name('habits.index');
    Route::post('habits', [HabitController::class, 'store'])->name('habits.store');
    Route::put('habits/{habit}', [HabitController::class, 'update'])->name('habits.update');
    Route::delete('habits/{habit}', [HabitController::class, 'destroy'])->name('habits.destroy');
    Route::post('habits/{habit}/tap', [HabitController::class, 'tap'])->name('habits.tap');

    Route::get('shop', [ShopController::class, 'index'])->name('shop');
    Route::post('shop/buy', [ShopController::class, 'buyItem'])->name('shop.buy');
    Route::post('rewards', [ShopController::class, 'storeReward'])->name('rewards.store');
    Route::post('rewards/{reward}/buy', [ShopController::class, 'buyReward'])->name('rewards.buy');
    Route::delete('rewards/{reward}', [ShopController::class, 'destroyReward'])->name('rewards.destroy');

    Route::get('stats', StatsController::class)->name('stats');
});
