<?php

use App\Http\Controllers\ManagerController;
use App\Http\Controllers\MarketValueController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\SponsorController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TransferController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MatchController::class, 'index'])->name('home');

// PUBLIC PAGES
Route::get('/matches', [MatchController::class, 'fixtures'])->name('matches.index');
Route::get('/results', [MatchController::class, 'results'])->name('results.index');
Route::get('/standings', [MatchController::class, 'standings'])->name('standings.index');
Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
Route::get('/teams/{teamId}', [TeamController::class, 'show'])->whereNumber('teamId')->name('teams.show');
Route::get('/players', [PlayerController::class, 'index'])->name('players.index');
Route::get('/sponsors', [SponsorController::class, 'index'])->name('sponsors.index');
Route::get('/managers', [ManagerController::class, 'index'])->name('managers.index');
Route::get('/market-values', [MarketValueController::class, 'index'])->name('market-values.index');
Route::get('/transfers', [TransferController::class, 'index'])->name('transfers.index');
Route::get('/transfers/{id}', [TransferController::class, 'show'])->name('transfers.show');

// ADMIN
Route::get('/super-admin-fpl-2026', [MatchController::class, 'admin'])->name('admin.panel');

// Matches / fixtures / standings
Route::post('/super-admin-fpl-2026/create', [MatchController::class, 'create'])->name('admin.match.create');
Route::post('/super-admin-fpl-2026/update', [MatchController::class, 'update'])->name('admin.match.update');
Route::post('/super-admin-fpl-2026/delete', [MatchController::class, 'destroy'])->name('admin.match.delete');
Route::post('/super-admin-fpl-2026/standings/recalculate', [MatchController::class, 'recalculate'])->name('admin.standings.recalculate');

// Teams
Route::post('/super-admin-fpl-2026/team/create', [TeamController::class, 'store'])->name('admin.team.create');
Route::post('/super-admin-fpl-2026/team/update', [TeamController::class, 'update'])->name('admin.team.update');
Route::post('/super-admin-fpl-2026/team/delete', [TeamController::class, 'destroy'])->name('admin.team.delete');

// Players
Route::post('/super-admin-fpl-2026/player/create', [PlayerController::class, 'store'])->name('admin.player.create');
Route::post('/super-admin-fpl-2026/player/update', [PlayerController::class, 'update'])->name('admin.player.update');
Route::post('/super-admin-fpl-2026/player/delete', [PlayerController::class, 'destroy'])->name('admin.player.delete');

// Sponsors
Route::post('/super-admin-fpl-2026/sponsors/create', [SponsorController::class, 'store'])->name('admin.sponsors.create');
Route::post('/super-admin-fpl-2026/sponsors/update', [SponsorController::class, 'update'])->name('admin.sponsors.update');
Route::post('/super-admin-fpl-2026/sponsors/delete', [SponsorController::class, 'destroy'])->name('admin.sponsors.delete');

// Managers
Route::post('/super-admin-fpl-2026/managers/create', [ManagerController::class, 'store'])->name('admin.managers.create');
Route::post('/super-admin-fpl-2026/managers/update', [ManagerController::class, 'update'])->name('admin.managers.update');
Route::post('/super-admin-fpl-2026/managers/delete', [ManagerController::class, 'destroy'])->name('admin.managers.delete');

// Player market values
Route::post('/super-admin-fpl-2026/market-values/create', [MarketValueController::class, 'store'])->name('admin.market-values.create');
Route::post('/super-admin-fpl-2026/market-values/update', [MarketValueController::class, 'update'])->name('admin.market-values.update');
Route::post('/super-admin-fpl-2026/market-values/delete', [MarketValueController::class, 'destroy'])->name('admin.market-values.delete');

// Transfer news / blog
Route::post('/super-admin-fpl-2026/transfers/create', [TransferController::class, 'store'])->name('admin.transfers.create');
Route::post('/super-admin-fpl-2026/transfers/update', [TransferController::class, 'update'])->name('admin.transfers.update');
Route::post('/super-admin-fpl-2026/transfers/delete', [TransferController::class, 'destroy'])->name('admin.transfers.delete');