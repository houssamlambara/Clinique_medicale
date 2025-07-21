<?php

namespace App\Http\Controllers;

use App\Interfaces\IPrescriptionRepository;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class PrescriptionController extends Controller
{
    private IPrescriptionRepository $prescriptionRepository;

    public function __construct(IPrescriptionRepository $prescriptionRepository)
    {
        $this->prescriptionRepository = $prescriptionRepository;
    }

    

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'dossier_medical_id' => 'required|exists:dossier_medicals,id',
            'medecin_id' => 'required|exists:medecins,id',
            'medicament' => 'required|string|max:255',
        ]);

        try {
            $data = $request->all();

            $prescription = $this->prescriptionRepository->create($data);

            return response()->json([
                'success' => true,
                'message' => 'Prescription créée avec succès',
                'data' => $prescription
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la prescription',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'medicament' => 'sometimes|string|max:255',
        ]);

        try {
            $data = $request->all();

            $success = $this->prescriptionRepository->update($id, $data);
            
            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Prescription non trouvée'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Prescription mise à jour avec succès'
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
            $success = $this->prescriptionRepository->delete($id);
            
            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Prescription non trouvée'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Prescription supprimée avec succès'
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
            $prescriptions = $this->prescriptionRepository->getPrescriptionsByMedecin($medecinId);
            return response()->json([
                'success' => true,
                'data' => $prescriptions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des prescriptions',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function getByPatient(int $patientId): JsonResponse
    {
        try {
            $prescriptions = $this->prescriptionRepository->getPrescriptionsByPatient($patientId);
            return response()->json([
                'success' => true,
                'data' => $prescriptions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des prescriptions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getByDossier(int $dossierId): JsonResponse
    {
        try {
            $prescriptions = $this->prescriptionRepository->getPrescriptionsByDossier($dossierId);
            return response()->json([
                'success' => true,
                'data' => $prescriptions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des prescriptions',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
