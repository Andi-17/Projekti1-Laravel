<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
 
    public function handle($request, Closure $next)
    {
        $user = $request->user();
    
        if (!$user || $user->role?->name !== 'admin') {
            return response()->json([
                'message' => 'Forbidden - Admin only'
            ], 403);
        }
    
        return $next($request);
    }
}
