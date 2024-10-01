<?php

use \Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;

Route::get('/', [ContactController::class, 'index']);
Route::post('/upload', [ContactController::class, 'upload']);
Route::post('/create-contact', [ContactController::class, 'create']);
Route::get('/all-contacts', [ContactController::class, 'showAll']);
Route::get('/show-contact', [ContactController::class, 'show']);




