<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
    
});
use App\Http\Controllers\Api\AuthController;

Route::post('/register', [AuthController::class, 'register']);