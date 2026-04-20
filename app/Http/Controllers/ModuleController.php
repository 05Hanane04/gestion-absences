<?php
namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Http\Request;
class ModuleController extends Controller
{
    
    public function index()
    {
        return response()->json(
            Module::with(['filiere', 'professeur.user'])->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom'           => 'required|string',
            'code'          => 'required|string|unique:modules',
            'filiere_id'    => 'required|exists:filieres,id',
            'professeur_id' => 'required|exists:professeurs,id',
            'volume_horaire'=> 'nullable|integer',
        ]);
        $module = Module::create($request->all());
        return response()->json($module->load(['filiere', 'professeur.user']), 201);
    }

    public function update(Request $request, Module $module)
    {
        $module->update($request->all());
        return response()->json($module->load(['filiere', 'professeur.user']));
    }
    
    public function destroy(Module $module)
    {
        $module->delete();
        return response()->json(['message' => 'Supprimé avec succès']);
    }

    
}