<?php
use App\Http\Controllers\SocialMediaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AboutUsController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\OurServiceController;
use App\Http\Controllers\OurTeamController;
use App\Http\Controllers\TestimonyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {

    Route::get('/user', [AuthController::class, 'getUser']);
    // Route::get('/user', function(){
    //     try{
    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Username get!',
    //             'username' => Auth::user()->name,
    //         ]);
    //     } catch (\throwable $th){
    //         return response()->json([
    //             'status' => false,
    //             'message' => "Username doesn't exist",
    //             'username' => $th->getMessage(),
    //         ]);
    //     }
    // });

// Grouping routes for About Us
Route::prefix('about-us')->group(function () {
    Route::get('/', [AboutUsController::class, 'index']);
    Route::post('/add', [AboutUsController::class, 'store']);
    Route::post('/update/{id}', [AboutUsController::class, 'update']);
    Route::delete('/delete/{id}', [AboutUsController::class, 'destroy']);
});

// Grouping routes for Contact Us
Route::prefix('contact-us')->group(function () {
    Route::get('/', [ContactUsController::class, 'index']);
    Route::post('/add', [ContactUsController::class, 'store']);
    Route::post('/update/{id}', [ContactUsController::class, 'update']);
    Route::delete('/delete/{id}', [ContactUsController::class, 'destroy']);
});

// Grouping routes for Social Media
Route::prefix('social-media')->group(function () {
    Route::get('/', [SocialMediaController::class, 'index']);
});

// Grouping routes for Testimonies
Route::prefix('testimonies')->group(function () {
    Route::get('/', [TestimonyController::class, 'index']);
    Route::post('/add', [TestimonyController::class, 'store']);
    Route::post('/update/{id}', [TestimonyController::class, 'update']);
    Route::delete('/delete/{id}', [TestimonyController::class, 'destroy']);
});

// Grouping routes for Banner
Route::prefix('banner')->group(function () {
    Route::get('/', [BannerController::class, 'index']);
    Route::post('/add', [BannerController::class, 'store']);
    Route::post('/update/{id}', [BannerController::class, 'update']);
    Route::delete('/delete/{id}', [BannerController::class, 'destroy']);
});

// Grouping routes for Our Service
Route::prefix('our-service')->group(function () {
    Route::get('/', [OurServiceController::class, 'index']);
    Route::post('/add', [OurServiceController::class, 'store']);
    Route::post('/update/{id}', [OurServiceController::class, 'update']);
    Route::delete('/delete/{id}', [OurServiceController::class, 'destroy']);
});

// Grouping routes for Our Team
Route::prefix('our-team')->group(function () {
    Route::get('/', [OurTeamController::class, 'index']);
    Route::post('/add', [OurTeamController::class, 'store']);
    Route::post('/update/{id}', [OurTeamController::class, 'update']);
    Route::delete('/delete/{id}', [OurTeamController::class, 'destroy']);
});

});
