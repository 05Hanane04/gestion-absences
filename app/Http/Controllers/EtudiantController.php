<?php
namespace App\Http\Controllers;

use App\Models\Etudiant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EtudiantController extends Controller
{
    public function index()
    {
        return response()->json(
            Etudiant::with(['user', 'filiere'])->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom'        => 'required|string',
            'email'      => 'required|email|unique:users',
            'cne'        => 'required|string|unique:etudiants',
            'filiere_id' => 'required|exists:filieres,id',
            'groupe'     => 'required|string',
        ]);

        $user = User::create([
            'name'     => $request->nom,
            'email'    => $request->email,
            'password' => Hash::make($request->cne),
            'role'     => 'etudiant',
        ]);

        $etudiant = Etudiant::create([
            'user_id'    => $user->id,
            'filiere_id' => $request->filiere_id,
            'cne'        => $request->cne,
            'groupe'     => $request->groupe,
        ]);

        return response()->json($etudiant->load(['user', 'filiere']), 201);
    }

    public function update(Request $request, Etudiant $etudiant)
    {
        $etudiant->update($request->only(['cne', 'groupe', 'filiere_id']));
        if ($request->nom) {
            $etudiant->user->update(['name' => $request->nom]);
        }
        return response()->json($etudiant->load(['user', 'filiere']));
    }

    public function destroy(Etudiant $etudiant)
    {
        $etudiant->user->delete();
        return response()->json(['message' => 'Supprimé avec succès']);
    }

    public function absences(Etudiant $etudiant)
    {
        return response()->json(
            $etudiant->absences()->with(['seance.module'])->get()
        );
    }
}