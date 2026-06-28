<?php

namespace App\Http\Controllers;

use App\Models\Structure;
use App\Models\Incident;
use App\Models\Victime;
use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StructureController extends Controller
{
    public function index()
    {
        return response()->json(
            Structure::withCount('agents')->latest()->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom'  => 'required|string',
            'type' => 'required|in:pompiers,samu,police,gendarmerie,marine,protection_civile,autre',
        ]);

        $structure = Structure::create($request->only(
            'nom','sigle','type','region','adresse','telephone','email','responsable_nom','responsable_titre'
        ));
        return response()->json(['success' => true, 'structure' => $structure], 201);
    }

    public function update(Request $request, $id)
    {
        $structure = Structure::findOrFail($id);
        $structure->update($request->only(
            'nom','sigle','type','region','adresse','telephone','email','responsable_nom','responsable_titre'
        ));
        return response()->json(['success' => true, 'structure' => $structure]);
    }

    public function toggle($id)
    {
        $structure = Structure::findOrFail($id);
        $structure->update(['actif' => !$structure->actif]);
        return response()->json(['success' => true, 'actif' => $structure->actif]);
    }

    public function bilan(Request $request)
    {
        $annee  = $request->get('annee', date('Y'));
        $mois   = $request->get('mois', null);

        $query = Incident::whereYear('created_at', $annee);
        if ($mois) $query->whereMonth('created_at', $mois);

        $incidents = $query->get();
        $ids = $incidents->pluck('id');

        // Victimes liées
        $victimes = Victime::whereIn('incident_id', $ids)->get();

        return response()->json([
            'annee'   => $annee,
            'mois'    => $mois,
            'total_incidents' => $incidents->count(),
            'par_type' => [
                'incendie' => $incidents->where('type_urgence','incendie')->count(),
                'accident' => $incidents->where('type_urgence','accident')->count(),
                'medical'  => $incidents->where('type_urgence','medical')->count(),
                'autre'    => $incidents->where('type_urgence','autre')->count(),
            ],
            'par_statut' => [
                'en_attente'     => $incidents->where('statut','en_attente')->count(),
                'pris_en_charge' => $incidents->where('statut','pris_en_charge')->count(),
                'en_route'       => $incidents->where('statut','en_route')->count(),
                'sur_place'      => $incidents->where('statut','sur_place')->count(),
                'termine'        => $incidents->where('statut','termine')->count(),
            ],
            'par_mois' => collect(range(1,12))->map(fn($m) => [
                'mois'  => $m,
                'total' => $incidents->filter(fn($i) => (int)date('m', strtotime($i->created_at)) === $m)->count(),
            ]),
            'victimes' => [
                'total'    => $victimes->count(),
                'leger'    => $victimes->where('etat','leger')->count(),
                'grave'    => $victimes->where('etat','grave')->count(),
                'critique' => $victimes->where('etat','critique')->count(),
                'decede'   => $victimes->where('etat','decede')->count(),
                'inconnu'  => $victimes->where('etat','inconnu')->count(),
            ],
        ]);
    }

    public function exportCsv(Request $request)
    {
        $annee = $request->get('annee', date('Y'));
        $incidents = Incident::whereYear('created_at', $annee)
            ->with(['agent.structure'])
            ->latest()->get();

        $csv = "ID,Type,Statut,Adresse,Citoyen,Telephone,Date,Structure\n";
        foreach ($incidents as $i) {
            $structure = $i->agent?->structure?->nom ?? 'Non assigné';
            $csv .= implode(',', [
                $i->id,
                $i->type_urgence,
                $i->statut,
                '"' . str_replace('"','""', $i->adresse ?? '') . '"',
                '"' . str_replace('"','""', $i->nom_citoyen ?? '') . '"',
                $i->telephone_citoyen ?? '',
                $i->created_at,
                '"' . $structure . '"',
            ]) . "\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=bilan_urgences_{$annee}.csv",
        ]);
    }
}
