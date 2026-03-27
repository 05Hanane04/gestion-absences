<?php
namespace App\Http\Controllers;

use App\Models\Professeur;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfesseurController extends Controller
{
    public function index()
    {
        return response()->json(
            Professeur::with(['user', 'modules.filiere'])->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom'        => 'required|string',
            'email'      => 'required|email|unique:users',
            'specialite' => 'required|string',
            'grade'      => 'nullable|string',
        ]);

        $user = User::create([
            'name'     => $request->nom,
            'email'    => $request->email,
            'password' => Hash::make('prof123'),
            'role'     => 'prof',
        ]);

        $prof = Professeur::create([
            'user_id'    => $user->id,
            'specialite' => $request->specialite,
            'grade'      => $request->grade ?? 'Professeur',
        ]);

        return response()->json($prof->load('user'), 201);
    }

    public function update(Request $request, Professeur $professeur)
    {
        $professeur->update($request->only(['specialite', 'grade']));
        if ($request->nom) {
            $professeur->user->update(['name' => $request->nom]);
        }
        return response()->json($professeur->load('user'));
    }

    public function destroy(Professeur $professeur)
    {
        $professeur->user->delete();
        return response()->json(['message' => 'Supprimé avec succès']);
    }
}