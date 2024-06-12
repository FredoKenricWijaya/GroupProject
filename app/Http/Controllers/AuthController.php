<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
/**
 * @OA\Post(
 *     path="/login",
 *     summary="Authenticate user",
 *     tags={"Login"},
 *     @OA\RequestBody(
 *         required=true,
 *         content={
 *             @OA\MediaType(
 *                 mediaType="multipart/form-data",
 *                 @OA\Schema(
 *                     required={"email", "password"},
 *                     @OA\Property(property="email", type="string", format="email"),
 *                     @OA\Property(property="password", type="string", format="password")
 *                 )
 *             )
 *         }
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login successful",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Login successful!"),
 *             @OA\Property(property="token", type="string"),
 *             @OA\Property(property="user", type="object",
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="name", type="string"),
 *                 @OA\Property(property="email", type="string", format="email"),
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Invalid Email/Password")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="The given data was invalid."),
 *             @OA\Property(property="errors", type="object")
 *         )
 *     )
 * )
 */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid Email/Password']);
        }

        $user = Auth::user();
        $tokenResult = $this->generateToken($user);

        return response()->json([
            'message' => 'Login successful!',
            'token' => $tokenResult['token'],
            'user' => $user
        ], 200);
    }

    private function generateToken($user)
    {
        $token = $user->createToken('Personal Access Token')->accessToken;
        return [
            'token' => $token
        ];
    }
}


