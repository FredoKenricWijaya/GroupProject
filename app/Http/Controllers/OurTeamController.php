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
     * Display a listing of the resource.
     */
    public function index()
    {
        $allData = OurTeam::all();
        return response()->json($allData, 200);
    }

    public function show($id)
    {
        $OurTeam = OurTeam::find($id);

        if (is_null($OurTeam)) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        return response()->json($OurTeam, 200);
    }

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

            $team->save();

            return response()->json([
                'message' => 'Data updated successfully!',
                'data' => $team
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error occurred while updating data: ' . $e->getMessage()], 500);
        }
    }

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
