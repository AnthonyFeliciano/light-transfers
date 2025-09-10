<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\{LoginController, RegisterController};
use App\Http\Controllers\{
    DashboardController, WalletController, TransactionController,
    NotificationController, UserController, SimulatorController, SettingsController
};

// Auth (fictício + real do Laravel)
Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('login.authenticate');
    Route::post('/login/demo', [LoginController::class, 'demo'])->name('login.demo');

    Route::get('/registrar', [RegisterController::class, 'show'])->name('register');
    Route::post('/registrar', [RegisterController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::view('/dashboard', 'dashboard.index')->name('dashboard');
    Route::view('/transferir', 'transfer.wizard')->name('transfer.wizard');
    Route::view('/carteira', 'wallet.extrato')->name('wallet.extrato');
    Route::view('/notificacoes', 'notification.index')->name('notification.index');
    Route::view('/usuarios', 'users.index')->name('users.index');
});
