<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Repositories\ImageRepository;
class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->paginate(10);

        return UserResource::collection($users);
    }

    public function show(User $user)
    {
        $user->load('role');

        return new UserResource($user);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role_id'  => ['nullable', 'exists:roles,id'],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id'  => $data['role_id'] ?? null,
        ]);

        return (new UserResource($user))
            ->response()
            ->setStatusCode(201);
    }


    
    public function update(Request $request, User $user)
    {
        try {
            $data = $request->validate([
                'name'     => ['sometimes', 'string', 'max:255'],
                'email'    => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
                'password' => ['sometimes', 'string', 'min:8'],
                'role_id'  => ['sometimes', 'nullable', 'exists:roles,id'],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422);
        }
    
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
    
        $user->update($data);
    
        return new UserResource($user->fresh('role'));
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ], 200);
    }

    
 

    public function uploadAvatar(Request $request, User $user, ImageRepository $imageRepo)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'max:2048']
        ]);
    
        $path = $imageRepo->replace(
            $request->file('avatar'),
            $user->avatar,
            'avatars'
        );
    
        $user->update(['avatar' => $path]);
    
        return response()->json([
            'message' => 'Avatar updated',
            'user' => $user
        ]);
    }

    
}