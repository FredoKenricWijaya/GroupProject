<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactUs;

class ContactUsController extends Controller
{
    public function index()
    {
        $contacts = ContactUs::all();
        return response()->json($contacts, 200);
    }

    public function store(StoreContactRequest $request)
    {
        try {
            $validatedData = $request->validated();

            $contact = ContactUs::create($validatedData);
            return response()->json($contact, 201);
        } catch (\Exception $e) {
            // Return error response
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(UpdateContactRequest $request, $id)
    {
        try {
            $contact = ContactUs::find($id);

            if (!$contact) {
                return response()->json(['message' => 'Contact not found'], 404);
            }

            $validatedData = $request->validated();

            $contact->update($validatedData);
            return response()->json($contact, 200);
        }
        catch (\Exception $e) {
            // Return error response
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function destroy($id)
    {
        $contact = ContactUs::find($id);

        if (!$contact) {
            return response()->json(['message' => 'Contact not found'], 404);
        }

        $contact->delete();
        return response()->json(['message' => 'Contact deleted successfully'], 200);
    }
}
