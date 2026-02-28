<?php
use App\Http\Controllers\TeamController;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\SponsorController;

Route::get('/', [MatchController::class, 'index']);

//ADMIN URL
Route::get('/super-admin-fpl-2026', [MatchController::class, 'admin'])->name('admin.panel');
Route::post('/super-admin-fpl-2026/update', [MatchController::class, 'update']);


Route::post('/super-admin-fpl-2026/create', [MatchController::class, 'create']);

// Teams page
Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');

// Admin: create team
Route::post('/super-admin-fpl-2026/team/create', [TeamController::class, 'store'])->name('admin.team.create');
 // Sponsors (public)
Route::get('/sponsors', [SponsorController::class, 'index'])->name('sponsors.index');

// Sponsors (admin)
Route::get('/super-admin-fpl-2026/sponsors', [SponsorController::class, 'admin'])->name('admin.sponsors');
Route::post('/super-admin-fpl-2026/sponsors/create', [SponsorController::class, 'store'])->name('admin.sponsors.create');