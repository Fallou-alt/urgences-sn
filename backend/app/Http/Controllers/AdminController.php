<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\Structure;
use App\Models\User;
use App\Models\Victime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard()
    {
        return response()->json([
            'stats' => [
                'structures'    => Structure::count(),
                'responsables'  => User::where('role', 'RESPONSABLE')->count(),
                'agents'        => User::where('role', 'AGENT')->count(),
                'incidents'     => Incident::count(),
                'victimes'      => Victime::count(),
                'en_attente'    => Incident::where('statut', 'EN_ATTENTE')->count(),
                'en_cours'      => Incident::whereIn('statut', ['AFFECTE', 'EN_ROUTE', 'SUR_PLACE'])->count(),
                'termines'      => Incident::where('statut', 'TERMINE')->count(),
                'aujourd_hui'   => Incident::whereDate('created_at', today())->count(),
            ],
            'recents' => Incident::with('structure:id,nom,sigle', 'agent:id,nom,prenom')
                ->latest()->take(10)->get(),
        ]);
    }

    // ─── Structures ───────────────────────────────────────────────────────────

    public function structures()
    {
        return response()->json(
            Structure::with('responsable:id,nom,prenom')
                ->withCount('agents')
                ->latest()->get()
        );
    }

    public function storeStructure(Request $request)
    {
        $request->validate([
            'nom'  => 'required|string',
            'type' => 'required|in:pompiers,samu,police,gendarmerie,marine,protection_civile,autre',
        ]);

        $structure = Structure::create($request->only(
            'nom', 'sigle', 'type', 'region', 'departement', 'commune',
            'adresse', 'telephone', 'email'
        ));

        return response()->json(['success' => true, 'structure' => $structure], 201);
    }

    public function updateStructure(Request $request, $id)
    {
        $structure = Structure::findOrFail($id);
        $structure->update($request->only(
            'nom', 'sigle', 'type', 'region', 'departement', 'commune',
            'adresse', 'telephone', 'email'
        ));
        return response()->json(['success' => true, 'structure' => $structure]);
    }

    public function deleteStructure($id)
    {
        Structure::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    public function toggleStructure($id)
    {
        $structure = Structure::findOrFail($id);
        $structure->update(['actif' => !$structure->actif]);
        return response()->json(['success' => true, 'actif' => $structure->actif]);
    }

    // ─── Responsables ─────────────────────────────────────────────────────────

    public function responsables()
    {
        return response()->json(
            User::where('role', 'RESPONSABLE')
                ->with('structure:id,nom,sigle')
                ->select('id', 'identifiant', 'nom', 'prenom', 'role', 'actif', 'structure_id', 'created_at')
                ->get()
        );
    }

    public function storeResponsable(Request $request)
    {
        $request->validate([
            'identifiant'  => 'required|unique:users',
            'mot_de_passe' => 'required|min:6',
            'nom'          => 'required',
            'prenom'       => 'required',
            'structure_id' => 'required|exists:structures,id',
        ]);

        $user = User::create([
            'identifiant'  => $request->identifiant,
            'mot_de_passe' => Hash::make($request->mot_de_passe),
            'nom'          => $request->nom,
            'prenom'       => $request->prenom,
            'role'         => 'RESPONSABLE',
            'structure_id' => $request->structure_id,
        ]);

        // Lier la structure à ce responsable
        Structure::where('id', $request->structure_id)
            ->update(['responsable_id' => $user->id]);

        return response()->json([
            'success' => true,
            'user' => $user->only('id', 'identifiant', 'nom', 'prenom', 'role', 'actif', 'structure_id'),
        ], 201);
    }

    public function toggleUser($id)
    {
        $user = User::findOrFail($id);
        $user->update(['actif' => !$user->actif]);
        return response()->json(['success' => true, 'actif' => $user->actif]);
    }

    // ─── Incidents (lecture seule) ────────────────────────────────────────────

    public function incidents()
    {
        return response()->json(
            Incident::with('structure:id,nom,sigle', 'agent:id,nom,prenom')
                ->latest()->get()
        );
    }

    // ─── Stats globales ───────────────────────────────────────────────────────

    public function stats(Request $request)
    {
        $annee = $request->get('annee', date('Y'));
        $mois  = $request->get('mois');

        $query = Incident::whereYear('created_at', $annee);
        if ($mois) $query->whereMonth('created_at', $mois);

        $incidents = $query->get();
        $ids       = $incidents->pluck('id');
        $victimes  = \App\Models\Victime::whereIn('incident_id', $ids)->get();

        return response()->json([
            'annee'           => $annee,
            'mois'            => $mois,
            'total_incidents' => $incidents->count(),
            'par_type' => [
                'incendie' => $incidents->where('type_urgence', 'incendie')->count(),
                'accident' => $incidents->where('type_urgence', 'accident')->count(),
                'medical'  => $incidents->where('type_urgence', 'medical')->count(),
                'autre'    => $incidents->where('type_urgence', 'autre')->count(),
            ],
            'par_statut' => [
                'EN_ATTENTE' => $incidents->where('statut', 'EN_ATTENTE')->count(),
                'AFFECTE'    => $incidents->where('statut', 'AFFECTE')->count(),
                'EN_ROUTE'   => $incidents->where('statut', 'EN_ROUTE')->count(),
                'SUR_PLACE'  => $incidents->where('statut', 'SUR_PLACE')->count(),
                'TERMINE'    => $incidents->where('statut', 'TERMINE')->count(),
                'ANNULE'     => $incidents->where('statut', 'ANNULE')->count(),
            ],
            'par_mois' => collect(range(1, 12))->map(fn($m) => [
                'mois'  => $m,
                'total' => $incidents->filter(
                    fn($i) => (int) date('m', strtotime($i->created_at)) === $m
                )->count(),
            ]),
            'victimes' => [
                'total'    => $victimes->count(),
                'leger'    => $victimes->where('etat', 'leger')->count(),
                'grave'    => $victimes->where('etat', 'grave')->count(),
                'critique' => $victimes->where('etat', 'critique')->count(),
                'decede'   => $victimes->where('etat', 'decede')->count(),
                'inconnu'  => $victimes->where('etat', 'inconnu')->count(),
            ],
        ]);
    }

    public function exportCsv(Request $request)
    {
        $annee     = $request->get('annee', date('Y'));
        $incidents = Incident::whereYear('created_at', $annee)
            ->with(['agent', 'structure'])
            ->latest()->get();

        $csv = "ID,Type,Statut,Adresse,Citoyen,Telephone,Date,Structure\n";
        foreach ($incidents as $i) {
            $structure = $i->structure?->nom ?? 'Non assigné';
            $csv .= implode(',', [
                $i->id,
                $i->type_urgence,
                $i->statut,
                '"' . str_replace('"', '""', $i->adresse ?? '') . '"',
                '"' . str_replace('"', '""', $i->citoyen_nom ?? '') . '"',
                $i->citoyen_telephone ?? '',
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
