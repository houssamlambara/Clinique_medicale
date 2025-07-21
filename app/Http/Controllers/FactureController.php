<?php

namespace App\Http\Controllers;

use App\Interfaces\IFactureRepository;
use App\Models\facture;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class FactureController extends Controller
{
    private IFactureRepository $factureRepository;

    public function __construct(IFactureRepository $factureRepository)
    {
        $this->factureRepository = $factureRepository;
    }

    public function index(): JsonResponse
    {
        try {
            $factures = $this->factureRepository->getAll();
            
            return response()->json([
                'success' => true,
                'data' => $factures
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur dans FactureController::index: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des factures',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        $facture = $this->factureRepository->findById($id);
        
        if (!$facture) {
            return response()->json([
                'success' => false,
                'message' => 'Facture non trouvée'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $facture
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'consultation_id' => 'required|exists:consultations,id',
            'date_facture' => 'required|date',
            'montant' => 'required|numeric|min:0',
            'est_paye' => 'sometimes|boolean',
            'date_paiement' => 'sometimes|date'
        ]);

        try {
            $data = $request->all();
            
            // Définir les valeurs par défaut
            if (!isset($data['est_paye'])) {
                $data['est_paye'] = false;
            }

            $facture = $this->factureRepository->create($data);

            return response()->json([
                'success' => true,
                'message' => 'Facture créée avec succès',
                'data' => $facture
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la facture',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'consultation_id' => 'sometimes|exists:consultations,id',
            'date_facture' => 'sometimes|date',
            'montant' => 'sometimes|numeric|min:0',
            'est_paye' => 'sometimes|boolean',
            'date_paiement' => 'sometimes|date'
        ]);

        try {
            $success = $this->factureRepository->update($id, $request->all());
            
            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Facture non trouvée'
                ], 404);
            }

            $facture = $this->factureRepository->findById($id);

            return response()->json([
                'success' => true,
                'message' => 'Facture mise à jour avec succès',
                'data' => $facture
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
            $success = $this->factureRepository->delete($id);
            
            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Facture non trouvée'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Facture supprimée avec succès'
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
     * Récupérer les factures d'une consultation
     */
    public function getByConsultation(int $consultationId): JsonResponse
    {
        $factures = $this->factureRepository->getByConsultation($consultationId);
        return response()->json([
            'success' => true,
            'data' => $factures
        ]);
    }

    /**
     * Récupérer les factures par date
     */
    public function getByDate(string $date): JsonResponse
    {
        $factures = $this->factureRepository->getByDate($date);
        return response()->json([
            'success' => true,
            'data' => $factures
        ]);
    }

    /**
     * Récupérer les factures non payées
     */
    public function getNonPayer(): JsonResponse
    {
        $factures = $this->factureRepository->getNonPayer();
        return response()->json([
            'success' => true,
            'data' => $factures
        ]);
    }

    /**
     * Récupérer les factures payées
     */
    public function getPayer(): JsonResponse
    {
        $factures = $this->factureRepository->getPayer();
        return response()->json([
            'success' => true,
            'data' => $factures
        ]);
    }

    /**
     * Marquer une facture comme payée
     */
    public function marquerCommePayer(int $id): JsonResponse
    {
        $facture = $this->factureRepository->findById($id);
        
        if (!$facture) {
            return response()->json([
                'success' => false,
                'message' => 'Facture non trouvée'
            ], 404);
        }

        $facture->marquerCommePayer();

        return response()->json([
            'success' => true,
            'message' => 'Facture marquée comme payée',
            'data' => $facture
        ]);
    }
}
