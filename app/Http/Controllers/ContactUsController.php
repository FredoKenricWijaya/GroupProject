<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactUs;

class ContactUsController extends Controller
{
        /**
     * @OA\Get(
     *     path="/contact_us",
     *     summary="Get all Contact Us datas",
     *     tags={"Contact Us"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="email", type="string", format="email"),
     *                 @OA\Property(property="phone", type="string"),
     *                 @OA\Property(property="address", type="string"),
     *                 @OA\Property(property="message", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No data found"
     *     )
     * )
     */
    public function index()
    {
        $contacts = ContactUs::all();
        return response()->json($contacts, 200);
    }

    /**
     * @OA\Post(
     *     path="/contact_us/add",
     *     summary="Create a new Contact Us data",
     *     tags={"Contact Us"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name", "email", "phone", "address", "message"},
     *            @OA\Property(
     *                property="name",
     *                type="string",
     *                description="Contact name",
     *                example="John Doe"
     *            ),
     *            @OA\Property(
     *                property="email",
     *                type="string",
     *                format="email",
     *                description="Contact email",
     *                example="john@example.com"
     *            ),
     *            @OA\Property(
     *                property="phone",
     *                type="string",
     *                description="Contact phone",
     *                example="1234567890"
     *            ),
     *            @OA\Property(
     *                property="address",
     *                type="string",
     *                description="Contact address",
     *                example="123 Street, City"
     *            ),
     *            @OA\Property(
     *                property="message",
     *                type="string",
     *                description="Contact message",
     *                example="This is a test message"
     *                   )
     *                 )
     *             )
     *         ),
     *     @OA\Response(
     *         response=201,
     *         description="Data created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="name", type="string"),
 *                 @OA\Property(property="email", type="string", format="email"),
 *                 @OA\Property(property="phone", type="string"),
 *                 @OA\Property(property="address", type="string"),
 *                 @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error occurred"
     *     )
     * )
     */

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
/**
 * @OA\Post(
 *     path="/contact_us/update/{id}",
 *     summary="Update an Contact Us data",
 *     tags={"Contact Us"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=false,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
     *            @OA\Property(
     *                property="name",
     *                type="string",
     *                description="Contact name",
     *                example="John Doe"
     *            ),
     *            @OA\Property(
     *                property="email",
     *                type="string",
     *                format="email",
     *                description="Contact email",
     *                example="john@example.com"
     *            ),
     *            @OA\Property(
     *                property="phone",
     *                type="string",
     *                description="Contact phone",
     *                example="1234567890"
     *            ),
     *            @OA\Property(
     *                property="address",
     *                type="string",
     *                description="Contact address",
     *                example="123 Street, City"
     *            ),
     *            @OA\Property(
     *                property="message",
     *                type="string",
     *                description="Contact message",
     *                example="This is a test message"
     *            ),
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Data updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="name", type="string"),
 *                 @OA\Property(property="email", type="string", format="email"),
 *                 @OA\Property(property="phone", type="string"),
 *                 @OA\Property(property="address", type="string"),
 *                 @OA\Property(property="message", type="string")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Data not found"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error occurred"
 *     )
 * )
 */
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

    /**
     * @OA\Delete(
     *     path="/contact_us/{id}",
     *     summary="Delete a contact",
     *     tags={"Contact Us"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Contact deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Contact deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Contact not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Contact not found")
     *         )
     *     )
     * )
     */
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
