<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnBUpdateRequest;
use App\Http\Requests\AnBStoreRequest;
use App\Models\AboutUs;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class AboutUsController extends Controller
{
        /**
     * @OA\Get(
     *     path="/about_us",
     *     summary="Get all About Us records",
     *     tags={"About Us"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="image", type="string"),
     *                 @OA\Property(property="description", type="string")
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
        $allData = AboutUs::all();
        $data = [];

        if ($allData->isNotEmpty()) {
            foreach ($allData as $item) {
                $imageUrl = Storage::disk('google')->url($item->image);
                $data[] = [
                    'id' => $item->id,
                    'image' => $imageUrl,
                    'description' => $item->description,
                ];
            }
            return response()->json($data, 200);
        } else {
            return response()->json([
                'message' => 'No data found',
                'data' => $data
            ], 200);
        }
    }

    /**
     * @OA\Post(
     *     path="/about_us/add",
     *     summary="Create a new About Us record",
     *     tags={"About Us"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"description", "image"},
     *                 @OA\Property(
     *                     property="description",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="image",
     *                     type="string",
     *                     format="binary"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Record created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="image", type="string"),
     *             @OA\Property(property="description", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error occurred"
     *     )
     * )
     */
    public function store(AnBStoreRequest $request)
    {
        try {
            // Get the file from the request
            $file = $request->file('image');

            // Store the file in the Google Drive disk
            $path = Storage::disk('google')->put('about_images', $file);

            // Create the AboutUs record in the database
            $AboutUs = AboutUs::create([
                'image' => $path, // Store the path in the database
                'description' => $request->description,
            ]);

            return response()->json($AboutUs, 201);
        } catch (\Exception $e) {
            // Return error response
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
/**
 * @OA\Post(
 *     path="/about_us/update/{id}",
 *     summary="Update an About Us record",
 *     tags={"About Us"},
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
 *                 @OA\Property(
 *                     property="description",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="image",
 *                     type="string",
 *                     format="binary"
 *                 )
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
 *                 @OA\Property(property="image", type="string"),
 *                 @OA\Property(property="description", type="string")
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
    public function update(AnBUpdateRequest $request, $id)
    {
        try {
            $about_us = AboutUs::find($id);

            if (is_null($about_us)) {
                return response()->json(['error' => 'Data not found'], 404);
            }

            if ($request->hasFile('image')) {
                // Delete the old image if it exists
                if ($about_us->image) {
                    Storage::disk('google')->delete($about_us->image);
                }

                // Store the new image with a unique name
                $newFileName = 'about_images/' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
                Storage::disk('google')->put($newFileName, file_get_contents($request->file('image')));
                $about_us->image = $newFileName;
            }

            if ($request->has('description')) {
                $about_us->description = $request->description;
            }

            $about_us->save();

            return response()->json([
                'message' => 'Data updated successfully!',
                'data' => $about_us
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error occurred while updating data: ' . $e->getMessage()], 500);
        }
    }

     /**
     * @OA\Delete(
     *     path="/about_us/delete/{id}",
     *     summary="Delete an About Us record",
     *     tags={"About Us"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Data successfully deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Data doesn't exist"
     *     )
     * )
     */
    public function destroy($id)
    {
        $AboutUs = AboutUs::find($id);

        if (is_null($AboutUs)) {
            return response()->json(['message' => "Data doesn't exist in this id"], 404);
        }

        // Delete the image file from Google Drive
        if ($AboutUs->image) {
            // Delete the file from the Google Drive folder
            Storage::disk('google')->delete($AboutUs->image);
        }

        // Delete the record from the database
        $AboutUs->delete();

        return response()->json(['message' => 'Data successfully deleted!'], 200);
    }
}



