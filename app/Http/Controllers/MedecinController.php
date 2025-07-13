<?php

namespace App\Http\Controllers;

use App\Interfaces\IMedecinRepository;
use App\Models\Medecin;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MedecinController extends Controller
{
    private IMedecinRepository $medecinRepository;

    public function __construct(IMedecinRepository $medecinRepository)
    {
        $this->medecinRepository = $medecinRepository;
    }

    public function index(): JsonResponse
    {
        $medecins = $this->medecinRepository->getAll();
        return response()->json([
            'success' => true,
            'data' => $medecins
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $medecin = $this->medecinRepository->findById($id);
        
        if (!$medecin) {
            return response()->json([
                'success' => false,
                'message' => 'Médecin non trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $medecin
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'specialite' => 'required|string|max:255',
            'numero_licence' => 'required|string|unique:medecins',
        ]);

        try {
            $medecin = $this->medecinRepository->create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Médecin créé avec succès',
                'data' => $medecin
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du médecin',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'specialite' => 'sometimes|string|max:255',
            'numero_licence' => 'sometimes|string|unique:medecins,numero_licence,' . $id,
        ]);

        try {
            $success = $this->medecinRepository->update($id, $request->all());
            
            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Médecin non trouvé'
                ], 404);
            }

            $medecin = $this->medecinRepository->findById($id);

            return response()->json([
                'success' => true,
                'message' => 'Médecin mis à jour avec succès',
                'data' => $medecin
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
            $success = $this->medecinRepository->delete($id);
            
            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Médecin non trouvé'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Médecin supprimé avec succès'
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
     * Récupérer les médecins par spécialité
     */
    public function getBySpecialite(string $specialite): JsonResponse
    {
        $medecins = $this->medecinRepository->getBySpecialite($specialite);
        return response()->json([
            'success' => true,
            'data' => $medecins
        ]);
    }

    // /**
    //  * Rechercher des médecins
    //  */
    // public function search(Request $request): JsonResponse
    // {
    //     $query = $request->get('q', '');
        
    //     if (empty($query)) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Terme de recherche requis'
    //         ], 400);
    //     }

    //     $medecins = $this->medecinRepository->search($query);
        
    //     return response()->json([
    //         'success' => true,
    //         'data' => $medecins
    //     ]);
    // }
}
