<?php

namespace App\Http\Controllers;

use App\Interfaces\IConsultationRepository;
use App\Models\Consultations;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ConsultationsController extends Controller
{
    private IConsultationRepository $consultationRepository;

    public function __construct(IConsultationRepository $consultationRepository)
    {
        $this->consultationRepository = $consultationRepository;
    }

    public function index(): JsonResponse
    {
        $consultations = $this->consultationRepository->getAll();
        return response()->json([
            'success' => true,
            'data' => $consultations
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $consultation = $this->consultationRepository->findById($id);
        
        if (!$consultation) {
            return response()->json([
                'success' => false,
                'message' => 'Consultation non trouvée'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $consultation
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user || $user->role !== 'medecin' || !$user->medecin) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé. Seuls les médecins peuvent créer des consultations.'
            ], 403);
        }

        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'montant' => 'required|numeric|min:0',
            'motif' => 'required|string|max:500',
            'statut' => 'sometimes|in:en_cours,terminée,annulée'
        ]);

        try {
            $data = $request->all();
            $data['medecin_id'] = $user->medecin->id;
            
            if (!isset($data['statut'])) {
                $data['statut'] = 'en_cours';
            }

            $consultation = $this->consultationRepository->create($data);

            return response()->json([
                'success' => true,
                'message' => 'Consultation créée avec succès',
                'data' => $consultation
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la consultation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        if (!$user || $user->role !== 'medecin' || !$user->medecin) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé. Seuls les médecins peuvent modifier des consultations.'
            ], 403);
        }

        $consultation = $this->consultationRepository->findById($id);
        if (!$consultation || $consultation->medecin_id !== $user->medecin->id) {
            return response()->json([
                'success' => false,
                'message' => 'Consultation non trouvée ou accès non autorisé'
            ], 404);
        }

        $request->validate([
            'patient_id' => 'sometimes|exists:patients,id',
            'montant' => 'sometimes|numeric|min:0',
            'motif' => 'sometimes|string|max:500',
            'statut' => 'sometimes|in:en_cours,terminée,annulée'
        ]);

        try {
            $success = $this->consultationRepository->update($id, $request->all());
            
            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Consultation non trouvée'
                ], 404);
            }

            $consultation = $this->consultationRepository->findById($id);

            return response()->json([
                'success' => true,
                'message' => 'Consultation mise à jour avec succès',
                'data' => $consultation
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
        $user = request()->user();
        if (!$user || $user->role !== 'medecin' || !$user->medecin) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé. Seuls les médecins peuvent supprimer des consultations.'
            ], 403);
        }

        $consultation = $this->consultationRepository->findById($id);
        if (!$consultation || $consultation->medecin_id !== $user->medecin->id) {
            return response()->json([
                'success' => false,
                'message' => 'Consultation non trouvée ou accès non autorisé'
            ], 404);
        }

        try {
            $success = $this->consultationRepository->delete($id);
            
            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Consultation non trouvée'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Consultation supprimée avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les consultations d'un patient
     */
    public function getByPatient(int $patientId): JsonResponse
    {
        $consultations = $this->consultationRepository->getByPatient($patientId);
        return response()->json([
            'success' => true,
            'data' => $consultations
        ]);
    }

    /**
     * Récupérer les consultations d'un médecin
     */
    public function getByMedecin(int $medecinId): JsonResponse
    {
        $consultations = $this->consultationRepository->getByMedecin($medecinId);
        return response()->json([
            'success' => true,
            'data' => $consultations
        ]);
    }

    /**
     * Récupérer les consultations par statut
     */
    public function getByStatut(string $statut): JsonResponse
    {
        $consultations = $this->consultationRepository->getByStatut($statut);
        return response()->json([
            'success' => true,
            'data' => $consultations
        ]);
    }
}
