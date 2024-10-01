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

    public function show(Request $request)
    {
        $allContacts = $this->showAll()->getData();
        $queriedData = $allContacts->data;

        $matchedContact = null;

        foreach ($queriedData as $data) {
            if (
                ($data->email == $request->email) ||
                ($data->name == $request->name) ||
                ($data->phone == $request->phone)
            ) {
                $matchedContact = $data;
                break;
            }
        }

        if ($matchedContact) {
            return response()->json([
                'data' => $matchedContact
            ], 200);
        } else {
            return response()->json([
                'status' => 'not found',
                'message' => 'No contact matches the provided email, name, or phone number'
            ], 404);
        }
    }

    public function index(Request $request, $perPage = 5)
    {
        $allContacts = $this->showAll()->getData();
        $stringJson = json_encode($allContacts, true);
        $toArray = json_decode($stringJson, true);
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;

        $currentPageItems = array_slice($toArray['data'], $offset, $perPage);

        $paginator = new LengthAwarePaginator(
            $currentPageItems,
            count($toArray['data']),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        return response()->json([
            "data" => $paginator
        ], 200);
    }//

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
        ]);

        try {
            $allContacts = $this->showAll()->getData();
            $contacts = $allContacts->data;

            $newContact = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ];

            $index = count($allContacts->data) + 1;
            $contacts[$index] = $newContact;

            $directory = storage_path('app/private/contacts');

            $files = glob($directory . '/*.json');

            $latestFile = max($files);
            // dd("WHY?");

            file_put_contents($latestFile, json_encode($contacts, JSON_PRETTY_PRINT));

            return response()->json(['message' => 'Contact created successfully', 'data' => $newContact], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Contact creation failed', 'error' => $e->getMessage()], 500);
        }
    }
}