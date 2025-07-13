<?php

namespace App\Http\Controllers;

use App\Interfaces\IMaterielRepository;
use App\Models\Materiels;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MaterielsController extends Controller
{
    private IMaterielRepository $materielRepository;

    public function __construct(IMaterielRepository $materielRepository)
    {
        $this->materielRepository = $materielRepository;
    }

    public function index(): JsonResponse
    {
        $materiels = $this->materielRepository->getAll();
        return response()->json([
            'success' => true,
            'data' => $materiels
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $materiel = $this->materielRepository->findById($id);
        
        if (!$materiel) {
            return response()->json([
                'success' => false,
                'message' => 'Matériel non trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $materiel
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        try {
            $data = $request->only(['nom', 'description']);
            $materiel = $this->materielRepository->create($data);

            return response()->json([
                'success' => true,
                'message' => 'Matériel créé avec succès',
                'data' => $materiel
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du matériel',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'nom' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
        ]);

        try {
            $data = $request->only(['nom', 'description']);
            $success = $this->materielRepository->update($id, $data);
            
            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Matériel non trouvé'
                ], 404);
            }

            $materiel = $this->materielRepository->findById($id);

            return response()->json([
                'success' => true,
                'message' => 'Matériel mis à jour avec succès',
                'data' => $materiel
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
            $success = $this->materielRepository->delete($id);
            
            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Matériel non trouvé'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Matériel supprimé avec succès'
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
     * Récupérer les matériels par nom
     */
    public function getByNom(string $nom): JsonResponse
    {
        $materiels = $this->materielRepository->getByNom($nom);
        return response()->json([
            'success' => true,
            'data' => $materiels
        ]);
    }

    /**
     * Récupérer les matériels par description
     */
    public function getByDescription(string $description): JsonResponse
    {
        $materiels = $this->materielRepository->getByDescription($description);
        return response()->json([
            'success' => true,
            'data' => $materiels
        ]);
    }

    /**
     * Rechercher des matériels
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        
        if (empty($query)) {
            return response()->json([
                'success' => false,
                'message' => 'Terme de recherche requis'
            ], 400);
        }

        $materiels = $this->materielRepository->search($query);
        
        return response()->json([
            'success' => true,
            'data' => $materiels
        ]);
    }
}
