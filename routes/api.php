<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CarBrandController;
use App\Http\Controllers\Api\CarModelController;
use App\Http\Controllers\Api\CarController;
use Laravel\Sanctum\PersonalAccessToken;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->get('/auth/user', [AuthController::class, 'user']);



Route::post('/refresh', function (Request $request) {
    $request->validate(['refresh_token' => 'required|string']);

    $token = PersonalAccessToken::findToken($request->refresh_token);

    if (!$token) {
        return response()->json(['message' => 'Invalid refresh token'], 401);
    }

    $user = $token->tokenable;

    return response()->json([
        'access_token' => $user->createToken('access-token')->plainTextToken,
        'token_type' => 'Bearer',
    ]);
});



Route::middleware(['auth:sanctum', 'admin'])->group(function () {

    
    Route::apiResource('users', UserController::class);
    Route::post('users/{user}/avatar', [UserController::class, 'uploadAvatar']);

    
    Route::apiResource('cars', CarController::class);
    Route::post('/cars/{car}/main-image', [CarController::class, 'uploadMainImage']);
    Route::post('/cars/{car}/images', [CarController::class, 'uploadImages']);
    Route::delete('/cars/{car}/main-image', [CarController::class, 'deleteMainImage']);
    Route::delete('/car-images/{image}', [CarController::class, 'deleteImage']);


    Route::apiResource('car-brands', CarBrandController::class);
    Route::post('/car-brands/{carBrand}/logo', [CarBrandController::class, 'uploadLogo']);

   
    Route::apiResource('car-models', CarModelController::class);

});