<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'identifiant'  => 'required|string',
            'mot_de_passe' => 'required|string',
        ]);

        $user = User::where('identifiant', $request->identifiant)->first();

        if (!$user || !Hash::check($request->mot_de_passe, $user->mot_de_passe)) {
            return response()->json(['message' => 'Identifiant ou mot de passe incorrect.'], 401);
        }

        if (!$user->actif) {
            return response()->json(['message' => 'Compte désactivé.'], 403);
        }

        $token = Str::random(60);
        $user->update(['token' => $token]);

        return response()->json([
            'token' => $token,
            'user'  => [
                'id'           => $user->id,
                'nom'          => $user->nom,
                'prenom'       => $user->prenom,
                'role'         => $user->role,
                'structure_id' => $user->structure_id,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->get('_user');
        if ($user) {
            $user->update(['token' => null]);
        }
        return response()->json(['success' => true]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'ancien'  => 'required',
            'nouveau' => 'required|min:6',
        ]);

        $user = $request->get('_user');

        if (!Hash::check($request->ancien, $user->mot_de_passe)) {
            return response()->json(['message' => 'Ancien mot de passe incorrect.'], 422);
        }

        $user->update(['mot_de_passe' => Hash::make($request->nouveau)]);
        return response()->json(['success' => true]);
    }

    public function changeProfil(Request $request)
    {
        $request->validate([
            'prenom' => 'required|string|max:100',
            'nom'    => 'required|string|max:100',
        ]);

        $user = $request->get('_user');
        $user->update([
            'prenom' => $request->prenom,
            'nom'    => $request->nom,
        ]);

        return response()->json([
            'success' => true,
            'user' => [
                'id'     => $user->id,
                'nom'    => $user->nom,
                'prenom' => $user->prenom,
                'role'   => $user->role,
            ],
        ]);
    }
}
