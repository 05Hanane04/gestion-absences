<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FiliereController;
use App\Http\Controllers\EtudiantController;
use App\Http\Controllers\ProfesseurController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\SeanceController;
use App\Http\Controllers\AbsenceController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);
    Route::apiResource('filieres', FiliereController::class);
    Route::apiResource('etudiants', EtudiantController::class);
    Route::get('etudiants/{etudiant}/absences', [EtudiantController::class, 'absences']);
    Route::apiResource('professeurs', ProfesseurController::class);
    Route::apiResource('modules', ModuleController::class);
    Route::apiResource('seances', SeanceController::class);
    Route::get('absences',[AbsenceController::class, 'index']);
    Route::post('absences',[AbsenceController::class, 'store']);
    Route::put('absences/{absence}/statut',[AbsenceController::class, 'updateStatut']);
    Route::get('absences/etudiant/{etudiantId}',[AbsenceController::class, 'parEtudiant']);
    Route::delete('absences/{absence}', [AbsenceController::class, 'destroy']);
});