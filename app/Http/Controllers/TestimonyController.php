<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTestimonyRequest;
use App\Models\Testimonies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestimonyController extends Controller
{
        /**
     * @OA\Get(
     *     path="/testimonies",
     *     summary="Get list of testimonies",
     *     tags={"Testimonies"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="image", type="string", example="https://drive.google.com/uc?id=example"),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="business_name", type="string", example="John's Business"),
     *                 @OA\Property(property="description", type="string", example="This is a testimony description")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No data found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No data found"),
     *             @OA\Property(property="data", type="array", @OA\Items())
     *         )
     *     )
     * )
     */
    public function index()
    {
        $allData = Testimonies::all();
        $data = [];

        if ($allData->isNotEmpty()) {
            foreach ($allData as $item) {
                $imageUrl = Storage::disk('google')->url($item->image);
                $data[] = [
                    'id' => $item->id,
                    'image' => $imageUrl,
                    'name' => $item->name,
                    'business_name' => $item->business_name,
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
     *     path="/testimonies/add",
     *     summary="Create a new testimony",
     *     tags={"Testimonies"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"image", "name", "business_name", "description"},
     *                 @OA\Property(property="image", type="string", format="binary"),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="business_name", type="string", example="John's Business"),
     *                 @OA\Property(property="description", type="string", example="This is a testimony description")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Testimony created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="image", type="string", example="testimony_images/example.jpg"),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="business_name", type="string", example="John's Business"),
     *             @OA\Property(property="description", type="string", example="This is a testimony description")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error occurred",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Error occurred while storing data: error message")
     *         )
     *     )
     * )
     */
    public function store(StoreTestimonyRequest $request)
    {
        try {
            // $path = $request->file('image')->store('testimony_images', 'public');
                        // Get the file from the request
            $file = $request->file('image');

                        // Store the file in the Google Drive disk
            $path = Storage::disk('google')->put('testimony_images', $file);

            $testimony = Testimonies::create([
                'image' => $path,
                'name' => $request->name,
                'business_name' => $request->business_name,
                'description' => $request->description,
            ]);

            return response()->json($testimony, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error occurred while storing data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/testimonies/update/{id}",
     *     summary="Update a testimony",
     *     tags={"Testimonies"},
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
     *                 @OA\Property(property="image", type="string", format="binary"),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="business_name", type="string", example="John's Business"),
     *                 @OA\Property(property="description", type="string", example="This is a testimony description")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Data updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Data updated successfully!"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="image", type="string", example="testimony_images/example.jpg"),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="business_name", type="string", example="John's Business"),
     *                 @OA\Property(property="description", type="string", example="This is a testimony description")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Data not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Data not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error occurred",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Error occurred while updating data: error message")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $testimony = Testimonies::find($id);

        if (is_null($testimony)) {
            return response()->json(['message' => 'Data not found'], 404);
        }

        try {
            if ($request->hasFile('image')) {
                // Delete the old image if it exists
                if ($testimony->image) {
                    Storage::disk('public')->delete($testimony->image);
                }

                // Store the new image with a unique name
                $newFileName = 'testimony_images/' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
                Storage::disk('google')->put($newFileName, file_get_contents($request->file('image')));
                $testimony->image = $newFileName;
            }

            if ($request->has('name')) {
                $testimony->name = $request->name;
            }

            if ($request->has('business_name')) {
                $testimony->business_name = $request->business_name;
            }

            if ($request->has('description')) {
                $testimony->description = $request->description;
            }

            $testimony->save();

            return response()->json(['message' => 'Data updated successfully!', 'data' => $testimony], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error occurred while updating data: ' . $e->getMessage()], 500);
        }
    }

     /**
     * @OA\Delete(
     *     path="/testimonies/delete/{id}",
     *     summary="Delete a testimony",
     *     tags={"Testimonies"},
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
     *             @OA\Property(property="message", type="string", example="Data successfully deleted!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Data not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Data not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error occurred",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Error occurred while deleting data: error message")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $testimonies = Testimonies::find($id);

        if (is_null($testimonies)) {
            return response()->json(['message' => 'Data not found'], 404);
        }

        try {
            // Delete the image file
            if ($testimonies->image) {
                Storage::disk('google')->delete($testimonies->image);
            }

            $testimonies->delete();

            return response()->json(['message' => 'Data successfully deleted!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error occurred while deleting data: ' . $e->getMessage()], 500);
        }
    }
}
