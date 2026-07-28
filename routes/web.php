<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\FavoriteController;
use Illuminate\Support\Facades\Route;

Route::get('/', function (\Illuminate\Http\Request $request) {
    if (auth()->check()) {
        if (!auth()->user()->hasVerifiedEmail()) {
            return view('welcome');
        }
        return redirect()->route('dashboard');
    }
    return view('welcome');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/dashboard/stats', [\App\Http\Controllers\DashboardController::class, 'stats'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.stats');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('projects', ProjectController::class);
    Route::resource('tasks', TaskController::class);
    Route::post('invitations', [InvitationController::class, 'store'])->name('invitations.store');
    Route::post('favorites/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    Route::post('time-logs/start', [\App\Http\Controllers\TimeLogController::class, 'start'])->name('time-logs.start');
    Route::post('time-logs/{timeLog}/stop', [\App\Http\Controllers\TimeLogController::class, 'stop'])->name('time-logs.stop');
    Route::post('time-logs', [\App\Http\Controllers\TimeLogController::class, 'store'])->name('time-logs.store');

    Route::get('reports/pdf', [\App\Http\Controllers\ReportController::class, 'download'])->name('reports.pdf');
});

require __DIR__.'/auth.php';
