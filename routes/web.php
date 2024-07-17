<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::name('users.')->middleware(['guest'])->group(function () {

    Route::get('/', function () {
        return view('login');
    })->name('loginPage');

    Route::get('/signup', function () {
        return view('signup');
    })->name('signupForm');

    Route::post('signup', [AuthController::class, 'signup'])->name('signup');
    Route::post('login', [AuthController::class, 'login'])->name('login');
});


Route::name('users.')->middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/logout',[AuthController::class,'logout'])->name('logout');
});
