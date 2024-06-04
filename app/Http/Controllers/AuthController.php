<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
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


