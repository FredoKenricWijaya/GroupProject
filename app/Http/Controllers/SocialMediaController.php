<?php

namespace App\Http\Controllers;

use App\Models\SocialMedia;
use Illuminate\Http\Request;

class SocialMediaController extends Controller
{
    public function index()
    {
        $socialMediaLinks = SocialMedia::all(['name', 'url', 'image']);

        return response()->json($socialMediaLinks);
    }
}

