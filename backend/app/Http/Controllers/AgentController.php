<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    private function getAgent(Request $request)
    {
        return $request->get('_user');
    }

    public function dashboard(Request $request)
    {
        $agent     = $this->getAgent($request);
        $incidents = Incident::where('agent_id', $agent->id)->get();

        return response()->json([
            'stats' => [
                'total'       => $incidents->count(),
                'en_cours'    => $incidents->whereIn('statut', ['AFFECTE', 'EN_ROUTE', 'SUR_PLACE'])->count(),
                'termines'    => $incidents->where('statut', 'TERMINE')->count(),
            ],
            'mission_en_cours' => Incident::where('agent_id', $agent->id)
                ->whereIn('statut', ['AFFECTE', 'EN_ROUTE', 'SUR_PLACE'])
                ->with('victimes')
                ->latest()->first(),
        ]);
    }

    public function mesMissions(Request $request)
    {
        $agent = $this->getAgent($request);
        return response()->json(
            Incident::where('agent_id', $agent->id)
                ->whereIn('statut', ['AFFECTE', 'EN_ROUTE', 'SUR_PLACE'])
                ->with('victimes')
                ->latest()->get()
        );
    }

    public function historique(Request $request)
    {
        $agent = $this->getAgent($request);
        return response()->json(
            Incident::where('agent_id', $agent->id)
                ->where('statut', 'TERMINE')
                ->latest()->get()
        );
    }

    public function updateStatut(Request $request, $id)
    {
        $request->validate([
            'statut' => 'required|in:EN_ROUTE,SUR_PLACE,TERMINE',
        ]);

        $agent    = $this->getAgent($request);
        $incident = Incident::where('agent_id', $agent->id)->findOrFail($id);

        $data = ['statut' => $request->statut];

        if ($request->statut === 'TERMINE' && $request->has('commentaire')) {
            $data['commentaire'] = $request->commentaire;
        }

        $incident->update($data);

        return response()->json(['success' => true, 'incident' => $incident]);
    }

    public function ajouterCommentaire(Request $request, $id)
    {
        $request->validate([
            'commentaire' => 'required|string',
        ]);

        $agent    = $this->getAgent($request);
        $incident = Incident::where('agent_id', $agent->id)->findOrFail($id);
        $incident->update(['commentaire' => $request->commentaire]);

        return response()->json(['success' => true]);
    }
}
