<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

// Для неавторизованных - редирект на login (стандартный роут Laravel)
Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->name('login');

// Защищенные роуты
Route::middleware(['auth'])->group(function () {

    Route::get('/index', function () {
        return redirect('/');
    });

    Route::get('/home', function () {
        return redirect('/');
    });

    Route::get('/', function () {
        return view('welcome');
    });
});
