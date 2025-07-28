<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DossierMedicalController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\RendezvousController;
use App\Http\Controllers\MedecinController;
use App\Http\Controllers\MaterielsController;
use App\Http\Controllers\ConsultationsController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\DepenseController;
use App\Http\Controllers\NotificationController;

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
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::get('/users/{id}', [AuthController::class, 'getUserById']);

    // Déconnexion
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Routes de rendez-vous
    Route::get('/rendezvous', [RendezvousController::class, 'index'])->name('rendezvous.index');
    Route::get('/rendezvous/{id}', [RendezvousController::class, 'show'])->name('rendezvous.show');
    Route::post('/rendezvous', [RendezvousController::class, 'store'])->name('rendezvous.store');
    Route::put('/rendezvous/{id}', [RendezvousController::class, 'update'])->name('rendezvous.update');
    Route::delete('/rendezvous/{id}', [RendezvousController::class, 'destroy'])->name('rendezvous.destroy');
    Route::get('/rendezvous/patient/{patientId}', [RendezvousController::class, 'getByPatient'])->name('rendezvous.by-patient');
    Route::get('/rendezvous/medecin/{medecinId}', [RendezvousController::class, 'getByMedecin'])->name('rendezvous.by-medecin');

    // Routes de gestion des médecins
    Route::get('/medecins', [MedecinController::class, 'index'])->name('medecins.index');
    Route::get('/medecins/{id}', [MedecinController::class, 'show'])->name('medecins.show');
    Route::post('/medecins', [MedecinController::class, 'store'])->name('medecins.store');
    Route::put('/medecins/{id}', [MedecinController::class, 'update'])->name('medecins.update');
    Route::delete('/medecins/{id}', [MedecinController::class, 'destroy'])->name('medecins.destroy');
    Route::get('/medecins/specialite/{specialite}', [MedecinController::class, 'getBySpecialite'])->name('medecins.by-specialite');

    // Routes de gestion des patients 
    Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
    Route::get('/patients/{id}', [PatientController::class, 'show'])->name('patients.show');
    Route::put('/patients/{id}', [PatientController::class, 'update'])->name('patients.update');
    Route::delete('/patients/{id}', [PatientController::class, 'destroy'])->name('patients.destroy');
    Route::get('patients/search', [PatientController::class, 'search'])->name('patients.search');
    Route::get('/patients/medecin/{medecinId}', [PatientController::class, 'getByMedecin'])->name('patients.by-medecin');

    // Routes de gestion des dossiers médicaux
    Route::post('dossiers', [DossierMedicalController::class, 'store'])->name('dossiers.store');
    Route::get('dossiers', [DossierMedicalController::class, 'index'])->name('dossiers.index');
    Route::delete('dossiers/{id}', [DossierMedicalController::class, 'destroy'])->name('dossiers.destroy');
    Route::put('dossiers/{id}', [DossierMedicalController::class, 'update'])->name('dossiers.update');
    Route::get('dossiers/{id}', [DossierMedicalController::class, 'show'])->name('dossiers.show');
    Route::get('dossiers/medecin/{medecinId}', [DossierMedicalController::class, 'getByMedecin'])->name('dossiers.by-medecin');
    Route::get('dossiers/patient/{patientId}', [DossierMedicalController::class, 'getDossiersByPatient'])->name('dossiers.by-patient');

    // Routes de gestion des prescriptions
    Route::post('/prescriptions', [PrescriptionController::class, 'store'])->name('prescriptions.store');
    Route::put('/prescriptions/{id}', [PrescriptionController::class, 'update'])->name('prescriptions.update');
    Route::delete('/prescriptions/{id}', [PrescriptionController::class, 'destroy'])->name('prescriptions.destroy');
    Route::get('/prescriptions/medecin/{medecinId}', [PrescriptionController::class, 'getByMedecin'])->name('prescriptions.by-medecin');
    Route::get('/prescriptions/patient/{patientId}', [PrescriptionController::class, 'getByPatient'])->name('prescriptions.by-patient');
    Route::get('/prescriptions/dossier/{dossierId}', [PrescriptionController::class, 'getByDossier'])->name('prescriptions.by-dossier');

    // Routes de gestion des infirmiers
    // Route::get('/infirmiers', [InfirmiersController::class, 'index'])->name('infirmiers.index');
    // Route::get('/infirmiers/{id}', [InfirmiersController::class, 'show'])->name('infirmiers.show');
    // Route::post('/infirmiers', [InfirmiersController::class, 'store'])->name('infirmiers.store');
    // Route::put('/infirmiers/{id}', [InfirmiersController::class, 'update'])->name('infirmiers.update');
    // Route::delete('/infirmiers/{id}', [InfirmiersController::class, 'destroy'])->name('infirmiers.destroy');
    // Route::get('/infirmiers/specialite/{specialite}', [InfirmiersController::class, 'getBySpecialite'])->name('infirmiers.by-specialite');

    // Routes de gestion des matériels
    Route::get('/materiels', [MaterielsController::class, 'index'])->name('materiels.index');
    Route::get('/materiels/{id}', [MaterielsController::class, 'show'])->name('materiels.show');
    Route::post('/materiels', [MaterielsController::class, 'store'])->name('materiels.store');
    Route::put('/materiels/{id}', [MaterielsController::class, 'update'])->name('materiels.update');
    Route::delete('/materiels/{id}', [MaterielsController::class, 'destroy'])->name('materiels.destroy');
    Route::get('/materiels/nom/{nom}', [MaterielsController::class, 'getByNom'])->name('materiels.by-nom');
    Route::get('/materiels/description/{description}', [MaterielsController::class, 'getByDescription'])->name('materiels.by-description');

    // Routes de gestion des consultations
    Route::post('/consultations', [ConsultationsController::class, 'store'])->name('consultations.store');
    Route::get('/consultations', [ConsultationsController::class, 'index'])->name('consultations.index');
    Route::get('/consultations/{id}', [ConsultationsController::class, 'show'])->name('consultations.show');
    Route::put('/consultations/{id}', [ConsultationsController::class, 'update'])->name('consultations.update');
    Route::delete('/consultations/{id}', [ConsultationsController::class, 'destroy'])->name('consultations.destroy');
    Route::get('/consultations/patient/{patientId}', [ConsultationsController::class, 'getByPatient'])->name('consultations.by-patient');
    Route::get('/consultations/medecin/{medecinId}', [ConsultationsController::class, 'getByMedecin'])->name('consultations.by-medecin');
    Route::get('/consultations/statut/{statut}', [ConsultationsController::class, 'getByStatut'])->name('consultations.by-statut');

    // Routes de gestion des factures
    Route::post('/factures', [FactureController::class, 'store'])->name('factures.store');
    Route::get('/factures', [FactureController::class, 'index'])->name('factures.index');
    Route::get('/factures/non-payer', [FactureController::class, 'getNonPayer'])->name('factures.non-payer');
    Route::get('/factures/payer', [FactureController::class, 'getPayer'])->name('factures.payer');
    Route::get('/factures/consultation/{consultationId}', [FactureController::class, 'getByConsultation'])->name('factures.by-consultation');
    Route::get('/factures/{id}', [FactureController::class, 'show'])->name('factures.show');
    Route::put('/factures/{id}', [FactureController::class, 'update'])->name('factures.update');
    Route::delete('/factures/{id}', [FactureController::class, 'destroy'])->name('factures.destroy');
    Route::post('/factures/{id}/payer', [FactureController::class, 'marquerCommePayer'])->name('factures.marquer-payee');

    // Routes de gestion des dépenses
    Route::post('/depenses', [DepenseController::class, 'store'])->name('depenses.store');
    Route::get('/depenses', [DepenseController::class, 'index'])->name('depenses.index');
    Route::get('/depenses/non-payer', [DepenseController::class, 'getNonPayer'])->name('depenses.non-payer');
    Route::get('/depenses/payer', [DepenseController::class, 'getPayer'])->name('depenses.payer');
    Route::get('/depenses/{id}', [DepenseController::class, 'show'])->name('depenses.show');
    Route::put('/depenses/{id}', [DepenseController::class, 'update'])->name('depenses.update');
    Route::delete('/depenses/{id}', [DepenseController::class, 'destroy'])->name('depenses.destroy');
    Route::post('/depenses/{id}/payer', [DepenseController::class, 'marquerCommePayer'])->name('depenses.marquer-payee');

    // Routes de gestion des notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications', [NotificationController::class, 'store'])->name('notifications.store');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::get('/notifications/patient/{patientId}', [NotificationController::class, 'getByPatient'])->name('notifications.by-patient');
});
