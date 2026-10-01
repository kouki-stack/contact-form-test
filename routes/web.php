<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TagController;

Route::get('/', [ContactController::class, 'index']);
Route::post('/contacts/confirm', [ContactController::class, 'confirm']);
Route::post('/contacts', [ContactController::class, 'store']);

Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', function () {
    Auth::logout();

    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/login');
});

Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
Route::get('/admin/contacts/{id}', [AdminController::class, 'show']);

Route::delete('/admin/contacts/{id}', [AdminController::class, 'destroy']);

Route::post('/admin/tags', [TagController::class, 'store']);

Route::get('/admin/tags/{id}/edit', [TagController::class, 'edit']);

Route::put('/admin/tags/{id}', [TagController::class, 'update']);

Route::delete('/admin/tags/{id}', [TagController::class, 'destroy']);

Route::get('/contacts/export', [AdminController::class, 'export']);