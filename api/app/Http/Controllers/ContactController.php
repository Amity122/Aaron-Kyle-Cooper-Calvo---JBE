<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:json'
        ]);

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        
        $file->storeAs('contacts', $filename);

        return response()->json(['message' => 'File uploaded successfully'], 200);
    }

    # Needs validation if File isn't a json
    # Needs try and exception block

    public function index()
    {
        return response()->json([
            "message" => "oh, hi!"
        ], 200);
    }//
}
