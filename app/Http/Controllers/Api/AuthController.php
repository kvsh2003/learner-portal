<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // login function
    public function login(Request $request)
    {
        $loginDetails = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'remember' => 'boolean'
        ]);

        $remember = $request->boolean('remember');

        $user = User::where('email', $loginDetails['email'])->first();

        // echo $user;

        if (!$user || !password_verify($loginDetails['password'],
                $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'invalid email or password'
            ], 401);
        }

        if ($remember) {
            $user->setRememberToken(Str::random(60));
            $user->save();
        }

        $token = $user->createToken('learner-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'token' => $token,
            'user' => $user,
            'remember' => $remember
        ], 200);
    }

    // logout function
    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'logout successfull'
        ],200);
    }

    // get detail about user

    public function me(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User Not Found'
            ],401);
        }

        return response()->json([
            'status'=> 'success',
            'user'=> $user
        ],200);
    }
}
