<?php

use \Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;

Route::get('/', [ContactController::class, 'index']);
Route::post('/upload', [ContactController::class, 'upload']);
Route::get('/all-contacts', [ContactController::class, 'showAll']);
Route::get('/show-contact', [ContactController::class, 'show']);




