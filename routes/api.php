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

    Route::get('/user', function(){
        try{
            return response()->json([
                'status' => true,
                'message' => 'Username taken!',
                'username' => Auth::user()->name,
            ]);
        } catch (\throwable $th){
            return response()->json([
                'status' => false,
                'message' => 'Username taken!',
                'username' => $th->getMessage(),
            ]);
        }
    });

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

    Route::get('/testimonies', [TestimonyController::class, 'index']);
    Route::get('/testimonies/{id}', [TestimonyController::class, 'show']);
    Route::post('/testimonies', [TestimonyController::class, 'store']);
    Route::post('/testimonies/{id}', [TestimonyController::class, 'update']);
    Route::delete('/testimonies/{id}', [TestimonyController::class, 'destroy']);

    Route::get('/banner', [BannerController::class, 'index']);
    Route::get('/banner/{id}', [BannerController::class, 'show']);
    Route::post('/banner', [BannerController::class, 'store']);
    Route::post('/banner/{id}', [BannerController::class, 'update']);
    Route::delete('/banner/{id}', [BannerController::class, 'destroy']);

    Route::get('/ourservice', [OurServiceController::class, 'index']);
    Route::get('/ourservice/{id}', [OurServiceController::class, 'show']);
    Route::post('/ourservice', [OurServiceController::class, 'store']);
    Route::post('/ourservice/{id}', [OurServiceController::class, 'update']);
    Route::delete('/ourservice/{id}', [OurServiceController::class, 'destroy']);

    Route::get('/ourteam', [OurTeamController::class, 'index']);
    Route::get('/ourteam/{id}', [OurTeamController::class, 'show']);
    Route::post('/ourteam', [OurTeamController::class, 'store']);
    Route::post('/ourteam/{id}', [OurTeamController::class, 'update']);
    Route::delete('/ourteam/{id}', [OurTeamController::class, 'destroy']);
});
