<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard()
    {
        return response()->json([
            'stats' => [
                'total'         => Incident::count(),
                'en_attente'    => Incident::where('statut', 'en_attente')->count(),
                'en_cours'      => Incident::whereIn('statut', ['pris_en_charge', 'en_route', 'sur_place'])->count(),
                'termines'      => Incident::where('statut', 'termine')->count(),
                'aujourd_hui'   => Incident::whereDate('created_at', today())->count(),
                'agents_actifs' => Agent::where('actif', true)->count(),
            ],
            'recents' => Incident::latest()->take(10)->get(),
        ]);
    }

    public function agents()
    {
        return response()->json(
            Agent::with('structure:id,nom,sigle')
                ->select('id', 'identifiant', 'nom', 'prenom', 'role', 'actif', 'structure_id', 'created_at')
                ->get()
        );
    }

    public function storeAgent(Request $request)
    {
        $request->validate([
            'identifiant'  => 'required|unique:agents',
            'mot_de_passe' => 'required|min:6',
            'nom'          => 'required',
            'prenom'       => 'required',
            'role'         => 'required|in:admin,pompier,samu',
        ]);

        $agent = Agent::create([
            'identifiant'  => $request->identifiant,
            'mot_de_passe' => Hash::make($request->mot_de_passe),
            'nom'          => $request->nom,
            'prenom'       => $request->prenom,
            'role'         => $request->role,
            'structure_id' => $request->structure_id,
        ]);

        return response()->json(['success' => true, 'agent' => $agent->only('id', 'identifiant', 'nom', 'prenom', 'role', 'actif')], 201);
    }

    public function toggleAgent($id)
    {
        $agent = Agent::findOrFail($id);
        $agent->update(['actif' => !$agent->actif]);
        return response()->json(['success' => true, 'actif' => $agent->actif]);
    }

    public function incidents()
    {
        return response()->json(Incident::with('agent:id,nom,prenom')->latest()->get());
    }

    public function deleteIncident($id)
    {
        Incident::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
