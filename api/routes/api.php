<?php

use \Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;

Route::get('/testapi', [ContactController::class, 'index']);
Route::post('/upload', [ContactController::class, 'upload']);

