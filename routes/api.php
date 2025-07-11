<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DossierMedicalController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Routes d'authentification (publiques)
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Routes protégées par authentification
Route::middleware('auth:sanctum')->group(function () {
    // Informations de l'utilisateur connecté
    Route::get('/user', [AuthController::class, 'user']);
    
    // Déconnexion
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    
    // Routes de gestion des patients
    Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
    Route::get('/patients/{id}', [PatientController::class, 'show'])->name('patients.show');
    Route::put('/patients/{id}', [PatientController::class, 'update'])->name('patients.update');
    Route::delete('/patients/{id}', [PatientController::class, 'destroy'])->name('patients.destroy');
    Route::get('patients/search', [PatientController::class, 'search'])->name('patients.search');
    
    // Routes de gestion des dossiers médicaux
    Route::apiResource('dossiers-medicaux', DossierMedicalController::class);
    Route::get('dossiers-medicaux/patient/{patientId}', [DossierMedicalController::class, 'getByPatient']);
    Route::post('dossiers-medicaux/{id}/antecedent', [DossierMedicalController::class, 'addAntecedent']);
    Route::post('dossiers-medicaux/{id}/allergie', [DossierMedicalController::class, 'addAllergie']);
    Route::get('dossiers-medicaux/{id}/historique', [DossierMedicalController::class, 'getHistorique']);
    
});
