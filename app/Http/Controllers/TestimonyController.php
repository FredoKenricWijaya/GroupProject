<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTestimonyRequest;
use App\Models\Testimonies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestimonyController extends Controller
{
    public function index()
    {
        $allData = Testimonies::all();
        $data = [];

        if ($allData->isNotEmpty()) {
            foreach ($allData as $item) {
                $imageUrl = Storage::disk('google')->url($item->image);
                $data[] = [
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

    public function show($id)
    {
        $testimonies = Testimonies::find($id);

        if (is_null($testimonies)) {
            return response()->json(['message' => 'Data not found'], 404);
        }

        return response()->json($testimonies, 200);
    }

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
