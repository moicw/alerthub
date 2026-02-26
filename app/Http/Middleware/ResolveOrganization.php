<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Organization;
use Illuminate\Http\Request;

class ResolveOrganization
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $organization = Organization::where('api_token', $token)->first();

        if (!$organization) {
            return response()->json([
                'message' => 'Invalid API token'
            ], 401);
        }

        // Attach tenant to request
        app()->instance('tenant', $organization);

        return $next($request);
    }
}