<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\consulta\DashboardController;
use App\Http\Controllers\admin\DashboardController as AdminDashboardController;

Route::view('/', 'home')->name('home');
Route::view('/blog', 'blog')->name('blog');
//Logout
Route::post('/logout', [LoginController::class,'logout'])->middleware('auth') ->name('logout');
Route::middleware('guest')->group(function () {
    Route::view('/register', 'auth/register')->name('register');

    // Login
    Route::get('/login', [LoginController::class, 'showLoginForm'])
         ->name('login');
    Route::post('/login', [LoginController::class, 'login'])
         ->name('login.post');
});

//rutas protegidas
Route::middleware(['auth'])->group(function () {

    //rutas administrador
    Route::get('/admin',[AdminDashboardController::class,'index'])->name('admin.dashboard');
    Route::get('/consulta/dashboard', [DashboardController::class, 'index'])->name('consulta.dashboard'); // <-- Podrías añadir middleware de rol más adelante
    ;

});

