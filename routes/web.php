<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectFolderController;
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
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::delete('/profile/avatar', [ProfileController::class, 'destroyAvatar'])->name('profile.avatar.destroy');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('projects', ProjectController::class);
    Route::delete('projects/{project}/users/{user}', [ProjectController::class, 'removeUser'])->name('projects.removeUser');
    Route::patch('projects/{project}/permissions/{user}', [ProjectController::class, 'updatePermissions'])->name('projects.updatePermissions');

    // Katalogi projektów
    Route::get('folders', [ProjectFolderController::class, 'index'])->name('folders.index');
    Route::post('folders', [ProjectFolderController::class, 'store'])->name('folders.store');
    Route::patch('folders/{folder}', [ProjectFolderController::class, 'update'])->name('folders.update');
    Route::delete('folders/{folder}', [ProjectFolderController::class, 'destroy'])->name('folders.destroy');
    Route::post('folders/assign', [ProjectFolderController::class, 'assignProject'])->name('folders.assign');
    Route::post('folders/unassign', [ProjectFolderController::class, 'unassignProject'])->name('folders.unassign');
    
    Route::resource('tasks', TaskController::class);
    Route::patch('tasks/{task}/toggle-complete', [TaskController::class, 'toggleComplete'])->name('tasks.toggleComplete');
    Route::delete('tasks/{task}/users/{user}', [TaskController::class, 'removeUser'])->name('tasks.removeUser');
    
    Route::get('invitations/suggestions', [InvitationController::class, 'suggestions'])->name('invitations.suggestions');
    Route::post('invitations', [InvitationController::class, 'store'])->name('invitations.store');
    Route::delete('invitations/{invitation}', [InvitationController::class, 'destroy'])->name('invitations.destroy');
    Route::post('favorites/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    
    Route::post('/notifications/{id}/read', function ($id) {
        auth()->user()->notifications()->findOrFail($id)->markAsRead();
        return response()->json(['success' => true]);
    })->name('notifications.read');

    Route::get('time-logs/resources', [\App\Http\Controllers\TimeLogController::class, 'resources'])->name('time-logs.resources');
    Route::get('time-logs/active', [\App\Http\Controllers\TimeLogController::class, 'activeTimer'])->name('time-logs.active');
    Route::post('time-logs/start', [\App\Http\Controllers\TimeLogController::class, 'start'])->name('time-logs.start');
    Route::post('time-logs/{timeLog}/stop', [\App\Http\Controllers\TimeLogController::class, 'stop'])->name('time-logs.stop');
    Route::post('time-logs', [\App\Http\Controllers\TimeLogController::class, 'store'])->name('time-logs.store');
    Route::post('time-logs/update-inline', [\App\Http\Controllers\TimeLogController::class, 'updateInline'])->name('time-logs.update-inline');

    Route::get('reports/pdf', [\App\Http\Controllers\ReportController::class, 'download'])->name('reports.pdf');
    Route::get('reports/timesheet', [\App\Http\Controllers\ReportController::class, 'timesheet'])->name('timesheet.index');
});

require __DIR__.'/auth.php';
