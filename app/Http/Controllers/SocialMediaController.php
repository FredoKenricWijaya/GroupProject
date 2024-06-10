<?php

namespace App\Http\Controllers;

use App\Models\SocialMedia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
class SocialMediaController extends Controller
{
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

