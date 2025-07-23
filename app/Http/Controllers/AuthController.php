<?php

namespace App\Http\Controllers;

use App\Models\Medecin;
use App\Models\Patient;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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

        try {
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

                case 'secretaire':
                case 'comptable':
                    // Pas de table spécialisée pour les secrétaires et comptables
                    // Ils utilisent seulement la table users
                    break;
            }

            // Créer le token Sanctum
            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Inscription réussie',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'inscription',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Identifiants invalides'
            ], 401);
        }

        // Supprimer les anciens tokens (optionnel)
        $user->tokens()->delete();

        // Créer un nouveau token
        $token = $user->createToken('auth-token')->plainTextToken;

        // Charger les informations complètes selon le rôle
        $userData = $this->getUserCompleteData($user);
        
        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie',
            'data' => [
                'user' => $userData,
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ]);
    }

    public function logout(Request $request)
    {
        // Supprimer le token actuel
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie'
        ]);
    }

    /**
     * Obtenir les informations de l'utilisateur connecté
     */
    public function user(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Récupère les informations complètes d'un utilisateur selon son rôle
     */
    private function getUserCompleteData(User $user): array
    {
        $userData = [
            'id' => $user->id,
            'nom' => $user->nom,
            'prenom' => $user->prenom,
            'email' => $user->email,
            'telephone' => $user->telephone,
            'role' => $user->role,
        ];

        // Ajouter les informations spécifiques selon le rôle
        switch ($user->role) {
            case 'patient':
                $patient = $user->patient;
                if ($patient) {
                    $userData['patient'] = [
                        'id' => $patient->id,
                        'date_naissance' => $patient->date_naissance,
                        'genre' => $patient->genre, // 'Homme' ou 'Femme'
                        'age' => $patient->age,
                        'full_name' => $patient->full_name,
                    ];
                }
                break;

            case 'medecin':
                $medecin = $user->medecin;
                if ($medecin) {
                    $userData['medecin'] = [
                        'id' => $medecin->id,
                        'specialite' => $medecin->specialite,
                        'numero_licence' => $medecin->numero_licence,
                    ];
                }
                break;

            case 'secretaire':
            case 'comptable':
                // Pas d'informations supplémentaires pour les secrétaires et comptables
                // Ils utilisent seulement les informations de base de la table users
                break;

            default:
                // Rôle non reconnu
                break;
        }

        return $userData;
    }
    
    public function profile(Request $request)
    {
        $user = $request->user();
        $userData = $this->getUserCompleteData($user);

        return response()->json([
            'success' => true,
            'data' => $userData
        ]);
    }


    public function getUserById($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non trouvé'
            ], 404);
        }

        $userData = $this->getUserCompleteData($user);

        return response()->json([
            'success' => true,
            'data' => $userData
        ]);
    }
}
