<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class AuthToken
{
    public function handle(Request $request, Closure $next, string $role = null)
    {
        $token = $request->bearerToken() ?? $request->query('token');
        $user  = $token ? User::where('token', $token)->where('actif', true)->first() : null;

        if (!$user) {
            return response()->json(['message' => 'Non authentifié.'], 401);
        }

        // ADMIN passe partout
        if ($role && $user->role !== $role && $user->role !== 'ADMIN') {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        $request->merge(['_user' => $user]);
        return $next($request);
    }
}
