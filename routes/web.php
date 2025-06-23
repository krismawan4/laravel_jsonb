<?php

use App\Http\Controllers\MasterController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/master', [MasterController::class, 'index'])->name('master.index');
    Route::get('/master-table-column/{master_table_id}', [MasterController::class, 'master_table_column'])->name('master-table-column.index');
    Route::get('/master-table-data/{master_table_id}', [MasterController::class, 'master_table_data'])->name('master-table-data.index');
});

require __DIR__.'/auth.php';
