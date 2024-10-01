<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        return response()->json([
            "message" => "oh, hi!"
        ], 200);
    }//
}
