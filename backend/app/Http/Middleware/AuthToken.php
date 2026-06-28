<?php

namespace App\Http\Middleware;

use App\Models\Agent;
use Closure;
use Illuminate\Http\Request;

class AuthToken
{
    public function handle(Request $request, Closure $next, string $role = null)
    {
        $token = $request->bearerToken() ?? $request->query('token');
        $agent = $token ? Agent::where('token', $token)->where('actif', true)->first() : null;

        if (!$agent) {
            return response()->json(['message' => 'Non authentifié.'], 401);
        }

        if ($role && $agent->role !== $role && $agent->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        $request->merge(['_agent' => $agent]);
        return $next($request);
    }
}
