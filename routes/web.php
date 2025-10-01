<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

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

    // Incidents list - only for managers
    Route::middleware(['auth', 'role:manager'])->get('/incidents', [IncidentController::class, 'index'])->name('incidents.index');

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

    // Manager-only incident actions
    Route::middleware(['auth', 'role:manager'])->group(function () {
        Route::get('/incidents/{incident}', [IncidentController::class, 'show'])->name('incidents.show');
        Route::get('/incidents/{incident}/edit', [IncidentController::class, 'edit'])->name('incidents.edit');
        Route::put('/incidents/{incident}', [IncidentController::class, 'update'])->name('incidents.update');
        Route::delete('/incidents/{incident}', [IncidentController::class, 'destroy'])->name('incidents.destroy');
        Route::patch('/incidents/{incident}/status', [IncidentController::class, 'updateStatus'])->name('incidents.updateStatus');
    });

    // Admin area
    Route::middleware(['auth', 'role:manager'])->group(function () {
        // Admin dashboard
        Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        
        // Admin incidents management
        Route::get('/admin/incidents', [AdminController::class, 'incidents'])->name('admin.incidents.index');
        Route::get('/admin/incidents/export', [AdminController::class, 'exportIncidents'])->name('admin.incidents.export');
        
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
