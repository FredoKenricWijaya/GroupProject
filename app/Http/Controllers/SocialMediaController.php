<?php

namespace App\Http\Controllers;

use App\Models\SocialMedia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
class SocialMediaController extends Controller
{
        /**
     * @OA\Get(
     *     path="/social_media",
     *     summary="Get list of all social media links",
     *     tags={"Social Media"},
     *     @OA\Response(
     *         response=200,
     *         description="List of social media links",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="url", type="string", format="url"),
     *                 @OA\Property(property="image", type="string", format="url")
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
        $socialMediaLinks = SocialMedia::all(['name', 'url', 'image']);
        $data = [];

        foreach ($socialMediaLinks as $item) {
            $imageUrl = Storage::disk('google')->url($item->image);
            $data[] = [
                'name' => $item->name,
                'url' => $item->url,
                'image' => $imageUrl,
            ];
        }

        return response()->json($data);
    }
}

