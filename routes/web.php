<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatientController;

Route::get('/', function () {
    return view('welcome');
});

// Routes d'authentification
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Routes Patient
Route::get('/patient/dashboard', function () {
    return view('patient.dashboard');
})->name('patient.dashboard');

Route::get('/rendezvous', function () {
    return view('patient.rendezvous');
})->name('rendezvous.index');

Route::get('/consultations', function () {
    return view('patient.consultations');
})->name('consultations.index');

Route::get('/patient/consultations', function () {
    return view('patient.consultations');
})->name('patient.consultations');

Route::get('/patient/dossiers', function () {
    return view('patient.dossiers');
})->name('patient.dossiers');

Route::get('/patient/prescriptions', function () {
    return view('patient.prescriptions');
})->name('patient.prescriptions');

Route::get('/patient/notifications', function () {
    return view('patient.notifications');
})->name('patient.notifications');

// Routes Médecin
Route::get('/medecin/dashboard', function () {
    return view('medecin.dashboard');
})->name('medecin.dashboard');

Route::get('/medecin/consultations', function () {
    return view('medecin.consultations');
})->name('medecin.consultations');

Route::get('/medecin/rendezvous', function () {
    return view('medecin.rendezvous');
})->name('medecin.rendezvous');

Route::get('/medecin/patients', function () {
    return view('medecin.patients');
})->name('medecin.patients');

Route::get('/medecin/dossiers', function () {
    return view('medecin.dossiers');
})->name('medecin.dossiers');

Route::get('/medecin/prescriptions', function () {
    return view('medecin.prescriptions');
})->name('medecin.prescriptions');

// Routes Comptable
Route::get('/comptable/dashboard', function () {
    return view('comptable.dashboard');
})->name('comptable.dashboard');

Route::get('/comptable/factures', function () {
    return view('comptable.factures');
})->name('comptable.factures');

Route::get('/comptable/depenses', function () {
    return view('comptable.depenses');
})->name('comptable.depenses');

// Routes Secrétaire
Route::get('/secretaire/dashboard', function () {
    return view('secretaire.dashboard');
})->name('secretaire.dashboard');

Route::get('/secretaire/rendezvous', function () {
    return view('secretaire.rendezvous');
})->name('secretaire.rendezvous');

Route::get('/secretaire/notifications', function () {
    return view('secretaire.notifications');
})->name('secretaire.notifications');

// Route de redirection tokens
Route::get('/auth/token/{token}', function ($token) {
    return redirect('/login')->with('message', 'Veuillez vous connecter via le formulaire');
})->name('auth.token');

// Routes protégées par authentification
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

    // Routes de gestion des patients
    Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
    Route::get('/patients/create', [PatientController::class, 'create'])->name('patients.create');
    Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');
    Route::get('/patients/{patient}', [PatientController::class, 'show'])->name('patients.show');
    Route::get('/patients/{patient}/edit', [PatientController::class, 'edit'])->name('patients.edit');
    Route::put('/patients/{patient}', [PatientController::class, 'update'])->name('patients.update');
    Route::delete('/patients/{patient}', [PatientController::class, 'destroy'])->name('patients.destroy');
});