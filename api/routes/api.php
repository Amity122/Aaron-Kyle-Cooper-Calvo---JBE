<?php

use \Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;

Route::get('/testapi', function () {
    dd("WOW!");
});
