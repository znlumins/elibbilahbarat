<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;

// Katalog & Detail
Route::get('/', [BookController::class, 'index'])->name('home');
Route::get('/book/{id}', [BookController::class, 'show'])->name('books.show');

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// User Only
Route::middleware('auth')->group(function () {
    Route::get('/my-loans', [BookController::class, 'myLoans'])->name('my.loans');
    Route::post('/payment/upload', [PaymentController::class, 'store'])->name('payment.store');
});