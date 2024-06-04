<?php

namespace App\Http\Controllers;

use App\Http\Requests\AboutUpdateRequest;
use App\Http\Requests\AboutUsRequest;
use App\Models\AboutUs;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AboutUsController extends Controller
{
    public function index()
    {
        $allData = AboutUs::all();
        return response()->json($allData, 200);
    }

    public function show($id)
    {
        $AboutUs = AboutUs::find($id);

        if (is_null($AboutUs)) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        return response()->json($AboutUs, 200);
    }

    public function store(AboutUsRequest $request)
    {
        try {
            $path = $request->file('image')->store('about_images', 'public');

            $AboutUs = AboutUs::create([
                'image' => $path,
                'description' => $request->description,
            ]);

            return response()->json($AboutUs, 201);
        }
        catch (\Exception $e) {
            // Return error response
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(AboutUpdateRequest $request, $id)
    {
        try {
            $about_us = AboutUs::find($id);

            if (is_null($about_us)) {
                return response()->json(['error' => 'Data not found'], 404);
            }

            if ($request->hasFile('image')) {
                // Delete the old image if it exists
                if ($about_us->image) {
                    Storage::disk('public')->delete($about_us->image);
                }

                // Store the new image
                $path = $request->file('image')->store('about_images', 'public');
                $about_us->image = $path;
            }

            if ($request->has('description')) {
                $about_us->description = $request->description;
            }

            $about_us->save();

            return response()->json([
                'message' => 'Record updated successfully!',
                'data' => $about_us
            ], 200);
        }
        catch (\Exception $e) {
            return response()->json(['error' => 'Error occurred while updating data: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $AboutUs = AboutUs::find($id);

        if (is_null($AboutUs)) {
            return response()->json(['message' => "Data doesn't exist in this id"], 404);
        }

        // Delete the image file
        if ($AboutUs->image) {
            Storage::disk('public')->delete($AboutUs->image);
        }

        $AboutUs->delete();

        return response()->json(['message' => 'Data successfully deleted!'], 200);
    }
}



