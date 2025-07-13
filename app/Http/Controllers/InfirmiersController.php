<?php

namespace App\Http\Controllers;

use App\Interfaces\IInfirmierRepository;
use App\Models\Infirmiers;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class InfirmiersController extends Controller
{
    private IInfirmierRepository $infirmierRepository;

    public function __construct(IInfirmierRepository $infirmierRepository)
    {
        $this->infirmierRepository = $infirmierRepository;
    }

    public function index(): JsonResponse
    {
        $infirmiers = $this->infirmierRepository->getAll();
        return response()->json([
            'success' => true,
            'data' => $infirmiers
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $infirmier = $this->infirmierRepository->findById($id);

        if (!$infirmier) {
            return response()->json([
                'success' => false,
                'message' => 'Infirmier non trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $infirmier
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'specialite' => 'required|string|max:255',
            'numero_licence' => 'required|string|unique:infirmiers',
        ]);

        try {
            $infirmier = $this->infirmierRepository->create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Infirmier créé avec succès',
                'data' => $infirmier
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de l\'infirmier',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'specialite' => 'sometimes|string|max:255',
            'numero_licence' => 'sometimes|string|unique:infirmiers,numero_licence,' . $id,
        ]);

        try {
            $success = $this->infirmierRepository->update($id, $request->all());

            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Infirmier non trouvé'
                ], 404);
            }

            $infirmier = $this->infirmierRepository->findById($id);

            return response()->json([
                'success' => true,
                'message' => 'Infirmier mis à jour avec succès',
                'data' => $infirmier
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
            $success = $this->infirmierRepository->delete($id);

            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Infirmier non trouvé'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Infirmier supprimé avec succès'
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
     * Récupérer les infirmiers par spécialité
     */
    public function getBySpecialite(string $specialite): JsonResponse
    {
        $infirmiers = $this->infirmierRepository->getBySpecialite($specialite);
        return response()->json([
            'success' => true,
            'data' => $infirmiers
        ]);
    }
}
