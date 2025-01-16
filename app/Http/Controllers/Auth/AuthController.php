<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([                 
            'username' => 'required',
            'password' => 'required'
        ]);

        if (Auth::attempt($request->only(['username', 'password']))) {
            /**
             * @var \App\Models\User
             */
            $user = Auth::user();

            $token = $user->createToken('acc_token')->plainTextToken;

            return response()->json([
                "message" => "login success",
                "token" => $token,
                "user" => $user
            ], 200);
        } else {
            return response()->json([
                "message" => "Wrong username or password"
            ], 401);
        }
    }

    public function register(Request $request)
    {
        $cred = $request->validate([
            'full_name' => 'required',
            'bio' => 'required|max:100',
            'username' => 'required|min:3|unique:users,username|alpha_num',
            'password' => Password::min(6),
            'is_active' => 'boolean'
        ]);

        /**
         * @var \App\Models\User
         */
        $user = User::create($cred);

        $token = $user->createToken('acc_token')->plainTextToken;

        return response()->json([
            "message" => "login success",
            "token" => $token,
            "user" => $user
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            "message" => "Logout success"
        ]);
    }
}
