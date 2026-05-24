<?php

namespace App\Http\Controllers;

use App\Models\Victime;
use App\Models\Incident;
use Illuminate\Http\Request;

class VictimeController extends Controller
{
    public function index($incidentId)
    {
        $victimes = Victime::where('incident_id', $incidentId)->get();
        return response()->json($victimes);
    }

    public function store(Request $request, $incidentId)
    {
        $request->validate([
            'nom'    => 'required|string',
            'prenom' => 'required|string',
            'etat'   => 'required|in:leger,grave,critique,decede,inconnu',
            'groupe_sanguin' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-,inconnu',
        ]);

        Incident::findOrFail($incidentId);

        $victime = Victime::create([
            'incident_id'  => $incidentId,
            'nom'          => $request->nom,
            'prenom'       => $request->prenom,
            'age'          => $request->age,
            'sexe'         => $request->sexe ?? 'inconnu',
            'telephone'    => $request->telephone,
            'groupe_sanguin'=> $request->groupe_sanguin ?? 'inconnu',
            'etat'         => $request->etat,
            'observations' => $request->observations,
        ]);

        return response()->json(['success' => true, 'victime' => $victime], 201);
    }

    public function destroy($id)
    {
        Victime::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
