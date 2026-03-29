<?php
namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Etudiant;
use App\Models\Seance;
use Illuminate\Http\Request;

class AbsenceController extends Controller
{
    public function index()
    {
        return response()->json(
            Absence::with(['etudiant.user', 'seance.module'])->latest()->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'etudiant_id' => 'required|exists:etudiants,id',
            'seance_id'   => 'required|exists:seances,id',
            'statut'      => 'nullable|in:non-justifiee,justifiee,en-attente',
        ]);

        $absence = Absence::firstOrCreate(
            ['etudiant_id' => $request->etudiant_id, 'seance_id' => $request->seance_id],
            ['statut' => $request->statut ?? 'non-justifiee']
        );

        return response()->json($absence->load(['etudiant.user', 'seance.module']), 201);
    }

    public function updateStatut(Request $request, Absence $absence)
    {
        $request->validate([
            'statut' => 'required|in:non-justifiee,justifiee,en-attente',
            'motif'  => 'nullable|string',
        ]);
        $absence->update($request->only(['statut', 'motif']));
        return response()->json($absence);
    }

    public function parEtudiant($etudiantId)
    {
        return response()->json(
            Absence::with(['seance.module.professeur.user'])
                ->where('etudiant_id', $etudiantId)
                ->latest()
                ->get()
        );
    }
    public function destroy(Absence $absence)
{
    $absence->delete();
    return response()->json(['message' => 'Supprimé avec succès']);
}
}