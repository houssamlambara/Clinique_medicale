<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

// Routes d'authentification
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Routes protégées par authentification
Route::middleware('auth')->group(function () {
    // Dashboard général (fallback)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // Dashboard Patient
    Route::get('/patient/dashboard', function () {
        return view('patient.dashboard');
    })->name('patient.dashboard');
    
    // Dashboard Médecin
    Route::get('/medecin/dashboard', function () {
        return view('medecin.dashboard');
    })->name('medecin.dashboard');
    
    // Dashboard Secrétaire
    Route::get('/secretaire/dashboard', function () {
        return view('secretaire.dashboard');
    })->name('secretaire.dashboard');
    
    // Dashboard Comptable
    Route::get('/comptable/dashboard', function () {
        return view('comptable.dashboard');
    })->name('comptable.dashboard');
});