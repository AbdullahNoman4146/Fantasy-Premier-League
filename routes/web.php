<?php
use App\Http\Controllers\TeamController;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MatchController;

Route::get('/', [MatchController::class, 'index']);

//ADMIN URL
Route::get('/super-admin-fpl-2026', [MatchController::class, 'admin'])->name('admin.panel');
Route::post('/super-admin-fpl-2026/update', [MatchController::class, 'update']);


Route::post('/super-admin-fpl-2026/create', [MatchController::class, 'create']);

// Teams page
Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');

// Admin: create team
Route::post('/super-admin-fpl-2026/team/create', [TeamController::class, 'store'])->name('admin.team.create');
