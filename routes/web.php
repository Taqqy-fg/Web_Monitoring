<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;

// 1. Halaman utama langsung diarahkan ke dashboard monitoring
Route::get('/', [ProjectController::class, 'index'])->name('dashboard');

// 2. Rute-Rute Kustom untuk Fitur Monitoring 
// (Menggunakan GET karena dipanggil melalui tag <a> / Link di Blade)
Route::get('/projects/ping-all', [ProjectController::class, 'pingAll'])->name('projects.pingAll');
Route::get('/projects/{project}/ping', [ProjectController::class, 'ping'])->name('projects.ping');
Route::get('/projects/{project}/export', [ProjectController::class, 'export'])->name('projects.export');

// (Menggunakan POST karena menerima kiriman data dari Form Tambah Sub-Halaman)
Route::post('/projects/{project}/child', [ProjectController::class, 'storeChild'])->name('projects.storeChild');

// 3. CRUD Resource Otomatis untuk management website (create, store, show, edit, update, destroy)
Route::resource('projects', ProjectController::class)->except(['index']);