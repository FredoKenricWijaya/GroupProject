<?php
use App\Http\Controllers\SocialMediaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AboutUsController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/about_us', [AboutUsController::class, 'index']);
    Route::get('/about_us/{id}', [AboutUsController::class, 'show']);
    Route::post('/about_us', [AboutUsController::class, 'store']);
    Route::post('/about_us/{id}', [AboutUsController::class, 'update']);
    Route::delete('/about_us/{id}', [AboutUsController::class, 'destroy']);

    Route::get('/contact_us', [ContactUsController::class, 'index']);
    Route::get('/contact_us/{id}', [ContactUsController::class, 'show']);
    Route::post('/contact_us', [ContactUsController::class, 'store']);
    Route::post('/contact_us/{id}', [ContactUsController::class, 'update']);
    Route::delete('/contact_us/{id}', [ContactUsController::class, 'destroy']);

    Route::get('/social_media', [SocialMediaController::class, 'index']);
});
