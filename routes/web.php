<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatientController;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

// Routes d'authentification
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Route pour rediriger après connexion API - Accès libre
Route::get('/patient/dashboard', function (Request $request) {
    Log::info('Accès libre à /patient/dashboard');
    return view('patient.dashboard');
})->name('patient.dashboard');

// Route pour la liste des rendez-vous - Accès libre
Route::get('/rendezvous', function (Request $request) {
    Log::info('Accès libre à /rendezvous');
    return view('patient.rendezvous');
})->name('rendezvous.index');

// Route pour la liste des consultations - Accès libre
Route::get('/consultations', function (Request $request) {
    Log::info('Accès libre à /consultations');
    return view('patient.consultations');
})->name('consultations.index');

// Route pour les consultations du patient - Accès libre
Route::get('/patient/consultations', function (Request $request) {
    Log::info('Accès libre à /patient/consultations');
    return view('patient.consultations');
})->name('patient.consultations');

// Route pour le dashboard médecin - Accès libre
Route::get('/medecin/dashboard', function (Request $request) {
    Log::info('Accès libre à /medecin/dashboard');
    return view('medecin.dashboard');
})->name('medecin.dashboard');

// Route pour les consultations du médecin - Accès libre
Route::get('/medecin/consultations', function (Request $request) {
    Log::info('Accès libre à /medecin/consultations');
    return view('medecin.consultations');
})->name('medecin.consultations');

// Route pour les rendez-vous du médecin - Accès libre
Route::get('/medecin/rendezvous', function (Request $request) {
    Log::info('Accès libre à /medecin/rendezvous');
    return view('medecin.rendezvous');
})->name('medecin.rendezvous');

// Route pour les patients du médecin - Accès libre
Route::get('/medecin/patients', function (Request $request) {
    Log::info('Accès libre à /medecin/patients');
    return view('medecin.patients');
})->name('medecin.patients');

// Route pour les dossiers médicaux du médecin - Accès libre
Route::get('/medecin/dossiers', function (Request $request) {
    Log::info('Accès libre à /medecin/dossiers');
    return view('medecin.dossiers');
})->name('medecin.dossiers');

// Route pour les dossiers médicaux du patient - Accès libre
Route::get('/patient/dossiers', function (Request $request) {
    Log::info('Accès libre à /patient/dossiers');
    return view('patient.dossiers');
})->name('patient.dossiers');

// Route pour les prescriptions du médecin - Accès libre
Route::get('/medecin/prescriptions', function (Request $request) {
    Log::info('Accès libre à /medecin/prescriptions');
    return view('medecin.prescriptions');
})->name('medecin.prescriptions');

// Route pour les prescriptions du patient - Accès libre
Route::get('/patient/prescriptions', function (Request $request) {
    Log::info('Accès libre à /patient/prescriptions');
    return view('patient.prescriptions');
})->name('patient.prescriptions');

Route::get('/auth/token/{token}', function ($token) {
    return redirect('/login')->with('message', 'Veuillez vous connecter via le formulaire');
})->name('auth.token');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();
        $role = $user->role;
        
        switch ($role) {
            case 'patient':
                return redirect('/patient/dashboard');
            case 'medecin':
                return redirect('/medecin/dashboard');
            case 'secretaire':
                return view('secretaire.dashboard');
            case 'comptable':
                return view('comptable.dashboard');
            default:
                return redirect('/login');
        }
    })->name('dashboard');

    Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
    Route::get('/patients/create', [PatientController::class, 'create'])->name('patients.create');
    Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');
    Route::get('/patients/{patient}', [PatientController::class, 'show'])->name('patients.show');
    Route::get('/patients/{patient}/edit', [PatientController::class, 'edit'])->name('patients.edit');
    Route::put('/patients/{patient}', [PatientController::class, 'update'])->name('patients.update');
    Route::delete('/patients/{patient}', [PatientController::class, 'destroy'])->name('patients.destroy');
});