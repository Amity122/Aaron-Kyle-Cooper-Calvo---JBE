<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
class ContactController extends Controller
{

    public function upload(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:json'
            ]);

            $file = $request->file('file');

            // Validate JSON content
            $content = file_get_contents($file->path());
            $json = json_decode($content);

            $filename = time() . '_' . $file->getClientOriginalName();

            $file->storeAs('contacts', $filename);

            return response()->json(['message' => 'File uploaded successfully'], 200);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
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

            file_put_contents($latestFile, json_encode($contacts, JSON_PRETTY_PRINT));

            return response()->json(['message' => 'Contact created successfully', 'data' => $newContact], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Contact creation failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $email)
    {
        $request->validate([
            'name' => 'string',
            'phone' => 'string',
        ]);

        try {
            $allContacts = $this->showAll()->getData();
            $contacts = $allContacts->data;

            foreach ($contacts as &$contact) {
                if ($contact->email === $email) {
                    if ($request->name != '') {
                        $contact->name = $request->name;
                    }
                    if ($request->phone != '') {
                        $contact->phone = $request->phone;
                    }

                    $directory = storage_path('app/private/contacts');

                    $files = glob($directory . '/*.json');

                    $latestFile = max($files);

                    file_put_contents($latestFile, json_encode($contacts, JSON_PRETTY_PRINT));
                    
                    return response()->json(['message' => 'Contact updated successfully', 'data' => $contact], 200);
                }
            }

            return response()->json(['message' => 'Contact not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Contact update failed', 'error' => $e->getMessage()], 500);
        }

    }
    public function destroy($email)
    {
        try {
            $directory = storage_path('app/private/contacts');
            $files = glob($directory . '/*.json');

            $latestFile = max($files);
            $contacts = json_decode(file_get_contents($latestFile), true);

            $contactIndex = array_search($email, array_column($contacts, 'email'));

            if ($contactIndex === false) {
                return response()->json(['message' => 'Contact not found'], 404);
            }

            array_splice($contacts, $contactIndex, 1);

            return response()->json(['message' => 'Contact deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Contact deletion failed', 'error' => $e->getMessage()], 500);
        }
    }
}
