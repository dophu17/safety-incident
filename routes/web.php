<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\AuthController;

Route::middleware('setLocale')->group(function () {
    Route::get('/', function () {
        return redirect()->route('incidents.index');
    });

    // Auth routes
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Locale switch
    Route::post('/locale', function () {
        request()->validate(['locale' => ['required', 'in:ja,vn']]);
        session(['locale' => request('locale')]);
        return back();
    })->name('locale.set');

    // Public feed
    Route::get('/incidents', [IncidentController::class, 'index'])->name('incidents.index');

    // Employee actions
    Route::middleware('auth')->group(function () {
        Route::get('/incidents/create', [IncidentController::class, 'create'])->name('incidents.create');
        Route::post('/incidents', [IncidentController::class, 'store'])->name('incidents.store');
        Route::get('/incidents/{incident}', [IncidentController::class, 'show'])->name('incidents.show');
        Route::get('/incidents/{incident}/edit', [IncidentController::class, 'edit'])->name('incidents.edit');
        Route::put('/incidents/{incident}', [IncidentController::class, 'update'])->name('incidents.update');
        Route::delete('/incidents/{incident}', [IncidentController::class, 'destroy'])->name('incidents.destroy');
    });

    // Admin area
    Route::middleware(['auth', 'role:manager'])->group(function () {
        Route::get('/admin/incidents', [IncidentController::class, 'index'])->name('admin.incidents.index');
    });
});

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public feed
Route::get('/incidents', [IncidentController::class, 'index'])->name('incidents.index');

// Employee actions
Route::middleware('auth')->group(function () {
    Route::get('/incidents/create', [IncidentController::class, 'create'])->name('incidents.create');
    Route::post('/incidents', [IncidentController::class, 'store'])->name('incidents.store');
    Route::get('/incidents/{incident}', [IncidentController::class, 'show'])->name('incidents.show');
    Route::get('/incidents/{incident}/edit', [IncidentController::class, 'edit'])->name('incidents.edit');
    Route::put('/incidents/{incident}', [IncidentController::class, 'update'])->name('incidents.update');
    Route::delete('/incidents/{incident}', [IncidentController::class, 'destroy'])->name('incidents.destroy');
});

// Admin area
Route::middleware(['auth', 'role:manager'])->group(function () {
    Route::get('/admin/incidents', [IncidentController::class, 'index'])->name('admin.incidents.index');
});
