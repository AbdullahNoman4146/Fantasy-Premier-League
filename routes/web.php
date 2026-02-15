<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MatchController;

Route::get('/', [MatchController::class, 'index']);

// ADMIN URL
Route::get('/super-admin-fpl-2026', [MatchController::class, 'admin']);
Route::post('/super-admin-fpl-2026/update', [MatchController::class, 'update']);


Route::post('/super-admin-fpl-2026/create', [MatchController::class, 'create']);

