<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

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

    public function showAll()
    {
        $directory = storage_path('app/private/contacts');

        $files = glob($directory . '/*.json');

        $latestFile = max($files);

        $stringJson = file_get_contents($latestFile);
        $cleanJson = json_decode($stringJson, true);

        return response()->json([
            'data' => $cleanJson
        ], 200);
    }

    public function show($email = '', $name = '', $phone_num = '')
    {
        $allContacts = $this->showAll();
        if (isset($allContacts[$email])) {
            dd($allContacts[$email]);
            // return $allContacts[$email];

        }
        // return response()->json([
        //     'data' => $cleanJson
        // ], 200);
    }

    public function index(Request $request, $perPage = 5)
    {
        $allContacts = $this->showAll()->getData();
        // dd($allContacts->data);
        // dd($theJson);
        $currentPage = $request->get('page', 1); 
        $offset = ($currentPage - 1) * $perPage;

        $currentPageItems = array_slice($allContacts->data, $offset, $perPage);

        $paginator = new LengthAwarePaginator(
            $currentPageItems,
            count($allContacts->data),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        return response()->json([
            "data" => $paginator
        ], 200);
    }//
}
