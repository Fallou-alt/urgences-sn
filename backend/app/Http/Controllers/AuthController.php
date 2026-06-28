<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $agent = Agent::where('identifiant', $request->identifiant)->first();

        if (!$agent || !Hash::check($request->mot_de_passe, $agent->mot_de_passe)) {
            return response()->json(['message' => 'Identifiant ou mot de passe incorrect.'], 401);
        }

        if (!$agent->actif) {
            return response()->json(['message' => 'Compte désactivé.'], 403);
        }

        $token = Str::random(60);
        $agent->update(['token' => $token]);

        return response()->json([
            'token' => $token,
            'user'  => [
                'id'     => $agent->id,
                'nom'    => $agent->nom,
                'prenom' => $agent->prenom,
                'role'   => $agent->role,
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $token = $request->bearerToken();
        Agent::where('token', $token)->update(['token' => null]);
        return response()->json(['success' => true]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'ancien'   => 'required',
            'nouveau'  => 'required|min:6',
        ]);

        $agent = $request->get('_agent');

        if (!Hash::check($request->ancien, $agent->mot_de_passe)) {
            return response()->json(['success' => false, 'message' => 'Ancien mot de passe incorrect.'], 422);
        }

        $agent->update(['mot_de_passe' => Hash::make($request->nouveau)]);
        return response()->json(['success' => true]);
    }

    public function changeProfil(Request $request)
    {
        $request->validate([
            'prenom' => 'required|string|max:100',
            'nom'    => 'required|string|max:100',
        ]);

        $agent = $request->get('_agent');
        $agent->update([
            'prenom' => $request->prenom,
            'nom'    => $request->nom,
        ]);

        return response()->json([
            'success' => true,
            'user' => [
                'id'     => $agent->id,
                'nom'    => $agent->nom,
                'prenom' => $agent->prenom,
                'role'   => $agent->role,
            ]
        ]);
    }
}
