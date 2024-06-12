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

    Route::get('/about_us', [AboutUsController::class, 'index']);
    Route::get('/about_us/{id}', [AboutUsController::class, 'show']);
    Route::post('/about_us/add', [AboutUsController::class, 'store']);
    Route::post('/about_us/update/{id}', [AboutUsController::class, 'update']);
    Route::delete('/about_us/delete/{id}', [AboutUsController::class, 'destroy']);

    Route::get('/contact_us', [ContactUsController::class, 'index']);
    Route::get('/contact_us/{id}', [ContactUsController::class, 'show']);
    Route::post('/contact_us/add', [ContactUsController::class, 'store']);
    Route::post('/contact_us/update/{id}', [ContactUsController::class, 'update']);
    Route::delete('/contact_us/delete/{id}', [ContactUsController::class, 'destroy']);

    Route::get('/social_media', [SocialMediaController::class, 'index']);

    Route::get('/testimonies', [TestimonyController::class, 'index']);
    Route::get('/testimonies/{id}', [TestimonyController::class, 'show']);
    Route::post('/testimonies/add', [TestimonyController::class, 'store']);
    Route::post('/testimonies/update/{id}', [TestimonyController::class, 'update']);
    Route::delete('/testimonies/delete/{id}', [TestimonyController::class, 'destroy']);

    Route::get('/banner', [BannerController::class, 'index']);
    Route::get('/banner/{id}', [BannerController::class, 'show']);
    Route::post('/banner/add', [BannerController::class, 'store']);
    Route::post('/banner/update/{id}', [BannerController::class, 'update']);
    Route::delete('/banner/delete/{id}', [BannerController::class, 'destroy']);

    Route::get('/ourservice', [OurServiceController::class, 'index']);
    Route::get('/ourservice/{id}', [OurServiceController::class, 'show']);
    Route::post('/ourservice/add', [OurServiceController::class, 'store']);
    Route::post('/ourservice/update/{id}', [OurServiceController::class, 'update']);
    Route::delete('/ourservice/delete/{id}', [OurServiceController::class, 'destroy']);

    Route::get('/ourteam', [OurTeamController::class, 'index']);
    Route::get('/ourteam/{id}', [OurTeamController::class, 'show']);
    Route::post('/ourteam/add', [OurTeamController::class, 'store']);
    Route::post('/ourteam/update/{id}', [OurTeamController::class, 'update']);
    Route::delete('/ourteam/delete/{id}', [OurTeamController::class, 'destroy']);
});
