<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\AuthController;

// ====================================================================
// GUEST ROUTES — Hanya bisa diakses jika BELUM login
// ====================================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Logout (butuh sudah login)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ====================================================================
// AUTHENTICATED ROUTES — Butuh login untuk akses
// ====================================================================
Route::middleware('auth')->group(function () {

    // --- Dashboard (semua role bisa lihat) ---
    Route::get('/', [ProjectController::class, 'index'])->name('dashboard');

    // --- PING — Semua role bisa melakukan ping ---
    Route::get('/projects/ping-all', [ProjectController::class, 'pingAll'])->name('projects.pingAll');
    Route::get('/projects/{project}/ping', [ProjectController::class, 'ping'])->name('projects.ping');

    // --- ADMIN ONLY — CRUD, Export, Detail, Sub-halaman ---
    Route::middleware('role:admin')->group(function () {
        Route::get('/projects/{project}/export', [ProjectController::class, 'export'])->name('projects.export');
        Route::post('/projects/{project}/child', [ProjectController::class, 'storeChild'])->name('projects.storeChild');
        Route::resource('projects', ProjectController::class)->except(['index']);
    });

});