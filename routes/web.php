<?php

use App\Http\Controllers\TeamController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\SponsorController;
use App\Http\Controllers\PlayerController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MatchController::class, 'index'])->name('home');

// ADMIN
Route::get('/super-admin-fpl-2026', [MatchController::class, 'admin'])->name('admin.panel');
Route::post('/super-admin-fpl-2026/update', [MatchController::class, 'update'])->name('admin.match.update');
Route::post('/super-admin-fpl-2026/create', [MatchController::class, 'create'])->name('admin.match.create');

// Teams
Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
Route::post('/super-admin-fpl-2026/team/create', [TeamController::class, 'store'])->name('admin.team.create');

// Players
Route::get('/players', [PlayerController::class, 'index'])->name('players.index');
Route::post('/super-admin-fpl-2026/player/create', [PlayerController::class, 'store'])->name('admin.player.create');

// Sponsors
Route::get('/sponsors', [SponsorController::class, 'index'])->name('sponsors.index');
Route::get('/super-admin-fpl-2026/sponsors', [SponsorController::class, 'admin'])->name('admin.sponsors');
Route::post('/super-admin-fpl-2026/sponsors/create', [SponsorController::class, 'store'])->name('admin.sponsors.create');