<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Organisation;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'phone' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'Bad Request', 'message' => 'Registration unsuccessful', 'errors' => $validator->errors()], 422);
        }

        $userData = User::create([
            'userId' =>  Str::uuid(),
            'firstName' => $request->input('firstName'),
            'lastName' => $request->input('lastName'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'phone' => $request->input('phone'),
        ]);

        $organisation = Organisation::create([
            'name' => $request->firstName . "'s Organisation",
            'description' => 'Default organisation for ' . $request->firstName,
        ]);

        // Attach user to organisation
        $organisation->users()->attach($userData);

        $token = JWTAuth::fromUser($userData);

        return response()->json([
            'status' => 'success',
            'message' => 'Registration successful',
            'data' => [
                'accessToken' => $token,
                'user' => $userData,
            ],
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'status' => 'Bad Request',
                'message' => 'Authentication failed',
                'statusCode' => 401,
            ], 401);
        }

        $user = auth()->user();

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'data' => [
                'accessToken' => $token,
                'user' => $user,
            ],
        ]);
    }
}
