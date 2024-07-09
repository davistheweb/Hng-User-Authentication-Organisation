<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function show($userId)
    {
        $authUser = Auth::user();

        if ($authUser->userId === $userId) {
            return response()->json([
                'status' => 'success',
                'message' => 'User retrieved successfully',
                'data' => [
                    'userId' => $authUser->userId,
                    'firstName' => $authUser->firstName,
                    'lastName' => $authUser->lastName,
                    'email' => $authUser->email,
                    'phone' => $authUser->phone,
                ],
            ], 200);
        }

        $user = User::find($userId);
        if ($user && $authUser->organisations->intersect($user->organisations)->isNotEmpty()) {
            return response()->json([
                'status' => 'success',
                'message' => 'User retrieved successfully',
                'data' => [
                    'userId' => $user->userId,
                    'firstName' => $user->firstName,
                    'lastName' => $user->lastName,
                    'email' => $user->email,
                    'phone' => $user->phone,
                ],
            ], 200);
        }
        
        return response()->json([
            'status' => 'error',
            'message' => 'User not found or unauthorized',
        ], 404);
    }
}

