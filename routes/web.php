<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\IncidentAdminController;

Route::middleware('setLocale')->group(function () {
    Route::get('/', function () {
        return view('welcome');
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

    // Language switch route for dropdown
    Route::get('/language/{locale}', function ($locale) {
        if (in_array($locale, ['ja', 'vn'])) {
            session(['locale' => $locale]);
        }
        return redirect()->back();
    })->name('language.switch');

    // Dashboard - redirect based on role
    Route::middleware('auth')->get('/dashboard', function () {
        if (Auth::user()->role === 'manager') {
            return redirect()->route('admin.dashboard');
        }
        // Employee dashboard - redirect to create incident
        return redirect()->route('incidents.create');
    })->name('dashboard');

    // Employee actions - only create
    Route::middleware('auth')->group(function () {
        Route::get('/incidents/create', [IncidentController::class, 'create'])->name('incidents.create');
        Route::post('/incidents', [IncidentController::class, 'store'])->name('incidents.store');
    });

    // Incident actions (used by admin panel)
    Route::middleware(['auth', 'role:manager'])->group(function () {
        Route::put('/incidents/{incident}', [IncidentController::class, 'update'])->name('incidents.update');
        Route::delete('/incidents/{incident}', [IncidentController::class, 'destroy'])->name('incidents.destroy');
        Route::patch('/incidents/{incident}/status', [IncidentController::class, 'updateStatus'])->name('incidents.updateStatus');
    });

    // Admin area
    Route::middleware(['auth', 'role:manager'])->group(function () {
        // Admin dashboard
        Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        
        // Admin incidents management
        Route::get('/admin/incidents', [IncidentAdminController::class, 'index'])->name('admin.incidents.index');
        Route::get('/admin/incidents/create', [IncidentAdminController::class, 'create'])->name('admin.incidents.create');
        Route::get('/admin/incidents/export', [IncidentAdminController::class, 'export'])->name('admin.incidents.export');
        Route::get('/admin/incidents/{incident}', [IncidentAdminController::class, 'show'])->name('admin.incidents.show');
        Route::get('/admin/incidents/{incident}/edit', [IncidentAdminController::class, 'edit'])->name('admin.incidents.edit');
        
        // Admin users management
        Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users.index');
        Route::get('/admin/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
        Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
        Route::get('/admin/users/{user}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
        Route::put('/admin/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
        Route::delete('/admin/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
        
        // Admin statistics
        Route::get('/admin/statistics', [AdminController::class, 'statistics'])->name('admin.statistics');
        
        // Company management
        Route::get('/admin/company', [AdminController::class, 'company'])->name('admin.company');
        Route::put('/admin/company', [AdminController::class, 'updateCompany'])->name('admin.company.update');
    });
});
