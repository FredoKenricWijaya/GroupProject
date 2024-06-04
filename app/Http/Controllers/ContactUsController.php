<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactUs;

class ContactUsController extends Controller
{
    public function index()
    {
        $contacts = ContactUs::all();
        return response()->json($contacts, 200);
    }

    public function show($id)
    {
        $contact = ContactUs::find($id);

        if (!$contact) {
            return response()->json(['message' => 'Contact not found'], 404);
        }

        return response()->json($contact, 200);
    }

    public function store(Request $request)
    {
        try{
            $validatedData = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'address' => 'required|string',
            'message' => 'required|string',
        ]);

            $contact = ContactUs::create($validatedData);
            return response()->json($contact, 201);
        }
        catch (\Exception $e) {
            // Return error response
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $contact = ContactUs::find($id);

            if (!$contact) {
                return response()->json(['message' => 'Contact not found'], 404);
            }

            $validatedData = $request->validate([
                'name' => 'sometimes|required|string',
                'email' => 'sometimes|required|email',
                'phone' => 'sometimes|required|string',
                'address' => 'sometimes|required|string',
                'message' => 'sometimes|required|string',
            ]);

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

