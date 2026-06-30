<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Http\Request;

class IncidentController extends Controller
{
    /**
     * Déclaration publique d'un incident par un citoyen.
     */
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
            'citoyen_nom'       => $request->citoyen_nom,
            'citoyen_telephone' => $request->citoyen_telephone,
            'statut'            => 'EN_ATTENTE',
        ]);

        return response()->json(['success' => true, 'id' => $incident->id], 201);
    }

    /**
     * Suivi public d'un incident par numéro.
     */
    public function suivi($id)
    {
        $incident = Incident::findOrFail($id);
        return response()->json([
            'id'           => $incident->id,
            'type_urgence' => $incident->type_urgence,
            'statut'       => $incident->statut,
            'adresse'      => $incident->adresse,
            'created_at'   => $incident->created_at,
            'updated_at'   => $incident->updated_at,
        ]);
    }

    /**
     * Stats publiques pour la page d'accueil.
     */
    public function stats()
    {
        return response()->json([
            'total'    => Incident::count(),
            'en_cours' => Incident::whereNotIn('statut', ['TERMINE', 'ANNULE'])->count(),
            'jour'     => Incident::whereDate('created_at', today())->count(),
            'agents'   => \App\Models\User::where('role', 'AGENT')->where('actif', true)->count(),
        ]);
    }
}
