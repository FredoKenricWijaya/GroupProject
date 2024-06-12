<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use App\Models\OurTeam;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class OurTeamController extends Controller
{
    /**
     * @OA\Get(
     *     path="/ourteam",
     *     summary="Get list of all team members",
     *     tags={"Our Team"},
     *     @OA\Response(
     *         response=200,
     *         description="List of team members",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="image", type="string", format="url"),
     *             @OA\Property(property="name", type="string"),
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
        $allData = OurTeam::all();
        $data = [];

        if ($allData->isNotEmpty()) {
            foreach ($allData as $item) {
                $imageUrl = Storage::disk('google')->url($item->image);
                $data[] = [
                    'id' => $item->id,
                    'image' => $imageUrl,
                    'name' => $item->name,
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
     *     path="/ourteam/add",
     *     summary="Create a new team member",
     *     tags={"Our Team"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name", "description", "image"},
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="image", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Team member created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="name", type="string"),
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
    public function store(StoreTeamRequest $request)
    {
        try {
            // Get the file from the request
            $file = $request->file('image');

            // Store the file in the Google Drive disk
            $path = Storage::disk('google')->put('Team_images', $file);

            // Create the OurTeam record in the database
            $OurTeam = OurTeam::create([
                'image' => $path, // Store the path in the database
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return response()->json($OurTeam, 201);
        } catch (\Exception $e) {
            // Return error response
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

        /**
     * @OA\Post(
     *     path="/ourteam/add/{id}",
     *     summary="Update an existing team member",
     *     tags={"Our Team"},
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
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="image", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Team member updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Data updated successfully!"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
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
    public function update(UpdateTeamRequest $request, $id)
    {
        try {
            $team = OurTeam::find($id);

            if (is_null($team)) {
                return response()->json(['error' => 'Data not found'], 404);
            }

            if ($request->hasFile('image')) {
                // Delete the old image if it exists
                if ($team->image) {
                    Storage::disk('google')->delete($team->image);
                }

                // Store the new image with a unique name
                $newFileName = 'Team_images/' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
                Storage::disk('google')->put($newFileName, file_get_contents($request->file('image')));
                $team->image = $newFileName;
            }

            if ($request->has('description')) {
                $team->description = $request->description;
            }

            if ($request->has('name')) {
                $team->name = $request->name;
            }

            $team->save();

            return response()->json([
                'message' => 'Data updated successfully!',
                'data' => $team
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error occurred while updating data: ' . $e->getMessage()], 500);
        }
    }
    /**
     * @OA\Delete(
     *     path="/ourteam/delete/{id}",
     *     summary="Delete a team member",
     *     tags={"Our Team"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Team member deleted successfully",
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
        $OurTeam = OurTeam::find($id);

        if (is_null($OurTeam)) {
            return response()->json(['message' => "Data doesn't exist in this id"], 404);
        }

        // Delete the image file from Google Drive
        if ($OurTeam->image) {
            // Delete the file from the Google Drive folder
            Storage::disk('google')->delete($OurTeam->image);
        }

        // Delete the record from the database
        $OurTeam->delete();

        return response()->json(['message' => 'Data successfully deleted!'], 200);
    }
}
