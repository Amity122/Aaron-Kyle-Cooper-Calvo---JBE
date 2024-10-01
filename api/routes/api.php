<?php

use \Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;

Route::get('/', [ContactController::class, 'index']);
Route::get('/all-contacts', [ContactController::class, 'showAll']);
Route::get('/show-contact', [ContactController::class, 'show']);
Route::post('/upload', [ContactController::class, 'upload']);
Route::post('/create-contact', [ContactController::class, 'create']);
Route::put('/edit-contact/{email}', [ContactController::class, 'update']);
Route::delete('/delete-contact/{email}', [ContactController::class, 'destroy']);



