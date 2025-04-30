<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{

    public function handle(Request $request, Closure $next): Response
    {
        $admin = auth()->user();

        if (! $admin->is_admin) {
            return response()->json([
                'success' => false,
                'message' => 'У вас нет прав!',
            ], 403);
        }
        
        return $next($request);
    }
}
