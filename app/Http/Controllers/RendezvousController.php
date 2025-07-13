<?php

namespace App\Http\Controllers;

use App\Interfaces\IRendezvousRepository;
use App\Models\Rendezvous;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RendezvousController extends Controller
{
    private IRendezvousRepository $rendezvousRepository;

    public function __construct(IRendezvousRepository $rendezvousRepository)
    {
        $this->rendezvousRepository = $rendezvousRepository;
    }

    public function index(): JsonResponse
    {
        $rendezvous = $this->rendezvousRepository->getAll();
        return response()->json([
            'success' => true,
            'data' => $rendezvous
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $rendezvous = $this->rendezvousRepository->findById($id);
        
        if (!$rendezvous) {
            return response()->json([
                'success' => false,
                'message' => 'Rendez-vous non trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $rendezvous
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            // 'medecin_id' => 'required|exists:medecins,id',
            'date_rdv' => 'required|date|after:now',
        ]);

        try {
            $rendezvous = $this->rendezvousRepository->create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Rendez-vous créé avec succès',
                'data' => $rendezvous
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du rendez-vous',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'patient_id' => 'sometimes|exists:patients,id',
            'medecin_id' => 'sometimes|exists:medecins,id',
            'date_rdv' => 'sometimes|date',
        ]);

        try {
            $success = $this->rendezvousRepository->update($id, $request->all());
            
            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rendez-vous non trouvé'
                ], 404);
            }

            $rendezvous = $this->rendezvousRepository->findById($id);

            return response()->json([
                'success' => true,
                'message' => 'Rendez-vous mis à jour avec succès',
                'data' => $rendezvous
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
            $success = $this->rendezvousRepository->delete($id);
            
            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rendez-vous non trouvé'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Rendez-vous supprimé avec succès'
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
     * Récupérer les rendez-vous d'un patient
     */
    public function getByPatient(int $patientId): JsonResponse
    {
        $rendezvous = $this->rendezvousRepository->getByPatientId($patientId);
        return response()->json([
            'success' => true,
            'data' => $rendezvous
        ]);
    }

    /**
     * Récupérer les rendez-vous d'un médecin
     */
    public function getByMedecin(int $medecinId): JsonResponse
    {
        $rendezvous = $this->rendezvousRepository->getByMedecinId($medecinId);
        return response()->json([
            'success' => true,
            'data' => $rendezvous
        ]);
    }
}
