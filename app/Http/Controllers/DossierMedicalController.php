<?php

namespace App\Http\Controllers;

use App\Interfaces\IDossierMedicalRepository;
use App\Models\DossierMedical;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DossierMedicalController extends Controller
{
    private IDossierMedicalRepository $dossierRepository;

    public function __construct(IDossierMedicalRepository $dossierRepository)
    {
        $this->dossierRepository = $dossierRepository;
    }

    public function index(): JsonResponse
    {
        $dossiers = DossierMedical::with('patient.user')->get();
        return response()->json([
            'success' => true,
            'data' => $dossiers
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $dossier = DossierMedical::with('patient.user', 'prescriptions')->find($id);
        
        if (!$dossier) {
            return response()->json([
                'success' => false,
                'message' => 'Dossier médical non trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $dossier
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'groupe_sanguin' => 'nullable|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'note' => 'nullable|string',
        ]);

        try {
            $dossier = $this->dossierRepository->create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Dossier médical créé avec succès',
                'data' => $dossier
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du dossier médical',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'groupe_sanguin' => 'sometimes|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'note' => 'sometimes|string',
        ]);

        try {
            $dossier = DossierMedical::find($id);
            
            if (!$dossier) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dossier médical non trouvé'
                ], 404);
            }

            $dossier->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Dossier médical mis à jour avec succès',
                'data' => $dossier->fresh(['patient.user'])
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
            $dossier = DossierMedical::find($id);
            
            if (!$dossier) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dossier médical non trouvé'
                ], 404);
            }

            $dossier->delete();

            return response()->json([
                'success' => true,
                'message' => 'Dossier médical supprimé avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Obtenir le dossier médical d'un patient
    public function getByPatient(int $patientId): JsonResponse
    {
        $dossier = $this->dossierRepository->getByPatientId($patientId);
        
        if (!$dossier) {
            return response()->json([
                'success' => false,
                'message' => 'Dossier médical non trouvé pour ce patient'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $dossier
        ]);
    }


}
