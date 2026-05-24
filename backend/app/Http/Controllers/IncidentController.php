<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Http\Request;

class IncidentController extends Controller
{
    public function index()
    {
        return response()->json(
            Incident::with('agent:id,nom,prenom')->latest()->get()
        );
    }

    public function medical()
    {
        return response()->json(
            Incident::with('agent:id,nom,prenom')
                ->where('type_urgence', 'medical')
                ->latest()->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'type_urgence' => 'required|in:incendie,accident,medical,autre',
        ]);

        $incident = Incident::create([
            'type_urgence'      => $request->type_urgence,
            'latitude'          => $request->latitude,
            'longitude'         => $request->longitude,
            'adresse'           => $request->adresse,
            'description'       => $request->description,
            'nom_citoyen'       => $request->nom_citoyen,
            'telephone_citoyen' => $request->telephone_citoyen,
            'statut'            => 'en_attente',
        ]);

        return response()->json(['success' => true, 'id' => $incident->id], 201);
    }

    public function updateStatut(Request $request, $id)
    {
        $request->validate([
            'statut' => 'required|in:en_attente,pris_en_charge,en_route,sur_place,termine'
        ]);

        $incident = Incident::findOrFail($id);
        $incident->update(['statut' => $request->statut]);

        return response()->json(['success' => true, 'incident' => $incident]);
    }

    public function suivi($id)
    {
        $incident = Incident::findOrFail($id);
        return response()->json([
            'id'               => $incident->id,
            'type_urgence'     => $incident->type_urgence,
            'statut'           => $incident->statut,
            'adresse'          => $incident->adresse,
            'created_at'       => $incident->created_at,
            'updated_at'       => $incident->updated_at,
        ]);
    }

    public function stats()
    {
        return response()->json([
            'total'    => Incident::count(),
            'en_cours' => Incident::whereNotIn('statut', ['termine'])->count(),
            'jour'     => Incident::whereDate('created_at', today())->count(),
            'agents'   => \App\Models\Agent::where('actif', true)->count(),
        ]);
    }
}
