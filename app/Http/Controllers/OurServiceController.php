<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\OurService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class OurServiceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/ourservice",
     *     summary="Get list of all services",
     *     tags={"Our Service"},
     *     @OA\Response(
     *         response=200,
     *         description="List of services",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="image", type="string", format="url"),
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string")
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
        $allData = OurService::all();
        $data = [];

        if ($allData->isNotEmpty()) {
            foreach ($allData as $item) {
                $imageUrl = Storage::disk('google')->url($item->image);
                $data[] = [
                    'id' => $item->id,
                    'image' => $imageUrl,
                    'title' => $item->title,
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
     *     path="/ourservice/add",
     *     summary="Create a new service",
     *     tags={"Our Service"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"title", "description", "image"},
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="image", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Service created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="image", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error occurred"
     *     )
     * )
     */
    public function store(StoreServiceRequest $request)
    {
        try {
            // Get the file from the request
            $file = $request->file('image');

            // Store the file in the Google Drive disk
            $path = Storage::disk('google')->put('Service_images', $file);

            // Create the OurService record in the database
            $OurService = OurService::create([
                'image' => $path, // Store the path in the database
                'title' => $request->title,
                'description' => $request->description,
            ]);

            return response()->json($OurService, 201);
        } catch (\Exception $e) {
            // Return error response
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

        /**
     * @OA\Post(
     *     path="/ourservice/update/{id}",
     *     summary="Update an existing service",
     *     tags={"Our Service"},
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
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="image", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Service updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Data updated successfully!"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="image", type="string")
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
    public function update(UpdateServiceRequest $request, $id)
    {
        try {
            $Service = OurService::find($id);

            if (is_null($Service)) {
                return response()->json(['error' => 'Data not found'], 404);
            }

            if ($request->hasFile('image')) {
                // Delete the old image if it exists
                if ($Service->image) {
                    Storage::disk('google')->delete($Service->image);
                }

                // Store the new image with a unique name
                $newFileName = 'Service_images/' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
                Storage::disk('google')->put($newFileName, file_get_contents($request->file('image')));
                $Service->image = $newFileName;
            }

            if ($request->has('description')) {
                $Service->description = $request->description;
            }

            if ($request->has('title')) {
                $Service->title = $request->title;
            }

            $Service->save();

            return response()->json([
                'message' => 'Data updated successfully!',
                'data' => $Service
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error occurred while updating data: ' . $e->getMessage()], 500);
        }
    }

        /**
     * @OA\Delete(
     *     path="/ourservice/delete/{id}",
     *     summary="Delete a service",
     *     tags={"Our Service"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Service deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Data successfully deleted!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Data not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Data doesn't exist in this id")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $OurService = OurService::find($id);

        if (is_null($OurService)) {
            return response()->json(['message' => "Data doesn't exist in this id"], 404);
        }

        // Delete the image file from Google Drive
        if ($OurService->image) {
            // Delete the file from the Google Drive folder
            Storage::disk('google')->delete($OurService->image);
        }

        // Delete the record from the database
        $OurService->delete();

        return response()->json(['message' => 'Data successfully deleted!'], 200);
    }
}
