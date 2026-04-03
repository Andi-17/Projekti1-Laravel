<?php
namespace App\Http\Controllers\Api;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;



class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'role' => ['required', 'string', 'exists:roles,name'], // admin, member, etj.
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    
        $role = Role::where('name', $data['role'])->first();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => $role?->id,
        ]);
        
        if ($role) {
            $user->roles()->syncWithoutDetaching([$role->id]);
        }
        
        return response()->json([
            'message' => 'User registered successfully',
            'user' => new UserResource($user),
        ], 201);
    }

   
    public function login(Request $request)
{
    $data = $request->validate([
        'email' => ['required','email'],
        'password' => ['required']
    ]);

    $user = User::where('email',$data['email'])->first();

    if (!$user || !Hash::check($data['password'],$user->password)) {
        return response()->json([
            'status' => 'error',
            'message' => 'Email ose password gabim'
        ],401);
    }


        $accessToken = $user->createToken('access-token')->plainTextToken;
        return response()->json([
            'status'=> "success",
            'message' => 'Login successful',
            'user' => new UserResource($user),
            'access_token' => $accessToken,
            'token_type' => 'Bearer',
        ], 200);
    }



    public function user(Request $request)
{
    return response()->json([
        'status' => 'success',
        'user' => new UserResource($request->user())
    ]);
}


public function refresh(Request $request)
{
    $request->validate([
        'refresh_token' => 'required|string'
    ]);

    $token = PersonalAccessToken::findToken($request->refresh_token);

    if (!$token) {
        return response()->json(['message' => 'Invalid refresh token'], 401);
    }

    $user = $token->tokenable;

   
    $token->delete();

    return response()->json([
        'access_token' => $user->createToken('access-token')->plainTextToken,
        'token_type' => 'Bearer',
    ]);
}

}

























