<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SocialMedia;

class SocialMediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
                // Define social media data with image paths
                $socialMediaData = [
                    ['name' => 'Twitter', 'url' => 'https://twitter.com', 'image' => 'social_media_images/twitter.png'],
                    ['name' => 'Facebook', 'url' => 'https://facebook.com', 'image' => 'social_media_images/facebook.png'],
                    ['name' => 'Instagram', 'url' => 'https://instagram.com', 'image' => 'social_media_images/instagram.png'],
                    ['name' => 'TikTok', 'url' => 'https://tiktok.com', 'image' => 'social_media_images/tiktok.png'],
                ];

                // Seed data into the social_media table
                foreach ($socialMediaData as $data) {
                    SocialMedia::create($data);
                }
    }
}
