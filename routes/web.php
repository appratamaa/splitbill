<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KnapsackController;

Route::get('/', [KnapsackController::class, 'index']);
Route::post('/calculate', [KnapsackController::class, 'solve'])->name('calculate');