<?php

namespace App\Http\Controllers;

use App\Interfaces\IDepenseRepository;
use App\Models\Depense;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DepenseController extends Controller
{
    private IDepenseRepository $depenseRepository;

    public function __construct(IDepenseRepository $depenseRepository)
    {
        $this->depenseRepository = $depenseRepository;
    }

    public function index(): JsonResponse
    {
        $depenses = $this->depenseRepository->getAll();
        return response()->json([
            'success' => true,
            'data' => $depenses
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $depense = $this->depenseRepository->findById($id);
        
        if (!$depense) {
            return response()->json([
                'success' => false,
                'message' => 'Dépense non trouvée'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $depense
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'date_depense' => 'required|date',
            'description' => 'required|string|max:500',
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

            $depense = $this->depenseRepository->create($data);

            return response()->json([
                'success' => true,
                'message' => 'Dépense créée avec succès',
                'data' => $depense
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la dépense',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'date_depense' => 'sometimes|date',
            'description' => 'sometimes|string|max:500',
            'montant' => 'sometimes|numeric|min:0',
            'est_paye' => 'sometimes|boolean',
            'date_paiement' => 'sometimes|date'
        ]);

        try {
            $success = $this->depenseRepository->update($id, $request->all());
            
            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dépense non trouvée'
                ], 404);
            }

            $depense = $this->depenseRepository->findById($id);

            return response()->json([
                'success' => true,
                'message' => 'Dépense mise à jour avec succès',
                'data' => $depense
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
            $success = $this->depenseRepository->delete($id);
            
            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dépense non trouvée'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Dépense supprimée avec succès'
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
     * Récupérer les dépenses par date
     */
    public function getByDate(string $date): JsonResponse
    {
        $depenses = $this->depenseRepository->getByDate($date);
        return response()->json([
            'success' => true,
            'data' => $depenses
        ]);
    }

    /**
     * Récupérer les dépenses non payées
     */
    public function getNonPayer(): JsonResponse
    {
        $depenses = $this->depenseRepository->getNonPayer();
        return response()->json([
            'success' => true,
            'data' => $depenses
        ]);
    }

    /**
     * Récupérer les dépenses payées
     */
    public function getPayer(): JsonResponse
    {
        $depenses = $this->depenseRepository->getPayer();
        return response()->json([
            'success' => true,
            'data' => $depenses
        ]);
    }

    /**
     * Marquer une dépense comme payée
     */
    public function marquerCommePayer(int $id): JsonResponse
    {
        $depense = $this->depenseRepository->findById($id);
        
        if (!$depense) {
            return response()->json([
                'success' => false,
                'message' => 'Dépense non trouvée'
            ], 404);
        }

        $depense->marquerCommePayer();

        return response()->json([
            'success' => true,
            'message' => 'Dépense marquée comme payée',
            'data' => $depense
        ]);
    }
}
