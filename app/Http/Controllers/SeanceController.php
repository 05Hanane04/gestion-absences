<?php
namespace App\Http\Controllers;

use App\Models\Seance;
use Illuminate\Http\Request;

class SeanceController extends Controller
{
    public function index()
    {
        return response()->json(
            Seance::with(['module.filiere', 'module.professeur.user'])->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'module_id'   => 'required|exists:modules,id',
            'date'        => 'required|date',
            'heure_debut' => 'required',
            'heure_fin'   => 'required',
            'salle'       => 'nullable|string',
        ]);
        $seance = Seance::create($request->all());
        return response()->json($seance->load('module'), 201);
    }

    public function destroy(Seance $seance)
    {
        $seance->delete();
        return response()->json(['message' => 'Supprimé avec succès']);
    }
}