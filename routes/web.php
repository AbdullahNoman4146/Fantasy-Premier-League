<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MatchController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/', [MatchController::class, 'index']);

// SECRET ADMIN URL
Route::get('/super-admin-fpl-2026', [MatchController::class, 'admin']);
Route::post('/super-admin-fpl-2026/update', [MatchController::class, 'update']);
