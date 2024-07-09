<?php

namespace App\Http\Controllers;

use App\Models\Organisation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrganisationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!($user instanceof User)) {
            return response(['message' => 'User not found'], 404);
        }

        $organisations = $user->organisations()->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Organisations fetched successfully',
            'data' => [
                'organisations' => $organisations,
            ],
        ], 200);
    }

    public function show($orgId)
    {

        $user = Auth::user();


        $organisation = Organisation::find($orgId);

        if (!$organisation) {
            return response()->json([
                'status' => 'error',
                'message' => 'Organisation not found',
            ], 404);
        }

        if ($organisation->users()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Organisation fetched successfully',
                'data' => $organisation,
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 403);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:organisations',
            'description' => 'string|nullable',
        ]);

        $organisation = Organisation::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        $user = Auth::user();
        $organisation->users()->attach($user);

        return response()->json([
            'status' => 'success',
            'message' => 'Organisation created successfully',
            'data' => $organisation,
        ], 201);
    }

    public function addUser(Request $request, $orgId)
    {

        $request->validate([
            'Id' => 'required|string',
        ]);

        $organisation = Organisation::find($orgId);

        if (!$organisation) {
            return response()->json([
                'status' => 'error',
                'message' => 'Organisation not found',
            ], 404);
        }

        $user = User::find($request->Id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found',
            ], 404);
        }

        if ($organisation->users()->where('user_id', $request->Id)->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'User already exists in the organisation',
            ], 400);
        }

        $organisation->users()->attach($user);

        return response()->json([
            'status' => 'success',
            'message' => 'User added to organisation successfully',
        ], 200);
    }
}
