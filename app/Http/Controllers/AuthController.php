<?php

namespace App\Http\Controllers;

use App\Models\Medecin;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('register');
    }

    public function showLogin()
    {
        return view('login');
    }

    public function register(Request $request)
    {
        // Validation de base
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:3',
            'telephone' => 'required|string|max:20',
            'role' => 'required|in:patient,medecin,secretaire,comptable',
        ]);

        // Validation selon le rôle
        if ($request->role === 'patient') {
            $request->validate([
                'date_naissance' => 'required|date',
                'genre' => 'required|in:Homme,Femme',
            ]);
        }

        if ($request->role === 'medecin') {
            $request->validate([
                'specialite' => 'required|string',
                'numero_licence' => 'required|string|unique:medecins',
            ]);
        }

        // Créer l'utilisateur
        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'telephone' => $request->telephone,
            'role' => $request->role,
        ]);

        // Créer l'entité spécifique
        switch ($request->role) {
            case 'patient':
                Patient::create([
                    'user_id' => $user->id,
                    'date_naissance' => $request->date_naissance,
                    'genre' => $request->genre,
                ]);
                break;

            case 'medecin':
                Medecin::create([
                    'user_id' => $user->id,
                    'specialite' => $request->specialite,
                    'numero_licence' => $request->numero_licence,
                ]);
                break;
        }

        Auth::login($user);
        
        // Redirection selon le rôle après inscription
        switch ($user->role) {
            case 'patient':
                return redirect('/patient/dashboard');
            case 'medecin':
                return redirect('/medecin/dashboard');
            case 'secretaire':
                return redirect('/secretaire/dashboard');
            case 'comptable':
                return redirect('/comptable/dashboard');
            default:
                return redirect('/dashboard');
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ], $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            // Redirection selon le rôle
            $user = Auth::user();
            switch ($user->role) {
                case 'patient':
                    return redirect('/patient/dashboard');
                case 'medecin':
                    return redirect('/medecin/dashboard');
                case 'secretaire':
                    return redirect('/secretaire/dashboard');
                case 'comptable':
                    return redirect('/comptable/dashboard');
                default:
                    return redirect('/dashboard');
            }
        }

        throw ValidationException::withMessages([
            'email' => ['Les identifiants fournis ne correspondent pas à nos enregistrements.'],
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
