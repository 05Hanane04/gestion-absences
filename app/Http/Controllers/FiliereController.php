<?php
namespace App\Http\Controllers;

use App\Models\Filiere;
use Illuminate\Http\Request;

class FiliereController extends Controller
{
    public function index()
    {
        return response()->json(Filiere::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom'    => 'required|string',
            'code'   => 'required|string|unique:filieres',
            'niveau' => 'required|string',
        ]);
        $filiere = Filiere::create($request->all());
        return response()->json($filiere, 201);
    }

    public function update(Request $request, Filiere $filiere)
    {
        $filiere->update($request->all());
        return response()->json($filiere);
    }

    public function destroy(Filiere $filiere)
    {
        $filiere->delete();
        return response()->json(['message' => 'Supprimé avec succès']);
    }
}