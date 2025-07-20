<?php

namespace App\Http\Controllers;

use App\Interfaces\IPatientRepository;
use App\Interfaces\IDossierMedicalRepository;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PatientController extends Controller
{
    private IPatientRepository $patientRepository;
    private IDossierMedicalRepository $dossierRepository;

    public function __construct(
        IPatientRepository $patientRepository,
        IDossierMedicalRepository $dossierRepository
    ) {
        $this->patientRepository = $patientRepository;
        $this->dossierRepository = $dossierRepository;
    }

    public function index(): JsonResponse
    {
        $patients = $this->patientRepository->getAll();
        return response()->json([
            'success' => true,
            'data' => $patients
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $patient = $this->patientRepository->findById($id);
        
        if (!$patient) {
            return response()->json([
                'success' => false,
                'message' => 'Patient non trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $patient
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date_naissance' => 'required|date',
            'genre' => 'required|in:Homme,Femme',
        ]);

        try {
            $patient = $this->patientRepository->create($request->all());
            
            $this->dossierRepository->create([
                'patient_id' => $patient->id,
                'note' => '',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Patient créé avec succès',
                'data' => $patient
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du patient',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'nom' => 'sometimes|string|max:255',
            'prenom' => 'sometimes|string|max:255',
            'email' => 'sometimes|email',
            'telephone' => 'sometimes|string|max:20',
            'date_naissance' => 'sometimes|date',
            'genre' => 'sometimes|in:Homme,Femme',
        ]);

        try {
            $patient = Patient::with('user')->find($id);
            
            if (!$patient) {
                return response()->json([
                    'success' => false,
                    'message' => 'Patient non trouvé'
                ], 404);
            }

            $patient->user->update($request->only(['nom', 'prenom', 'email', 'telephone']));
            $patient->update($request->only(['date_naissance', 'genre']));

            return response()->json([
                'success' => true,
                'message' => 'Patient mis à jour avec succès',
                'data' => $patient->fresh(['user'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $success = $this->patientRepository->delete($id);
            
            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Patient non trouvé'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Patient supprimé avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getByMedecin(int $medecinId): JsonResponse
    {
        try {
            $consultations = \App\Models\Consultations::where('medecin_id', $medecinId)
                ->with(['patient.user'])
                ->get();

            $patients = $consultations->map(function($consultation) {
                return $consultation->patient;
            })->unique('id')->values();

            return response()->json([
                'success' => true,
                'data' => $patients
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des patients',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 