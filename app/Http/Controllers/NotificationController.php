<?php

namespace App\Http\Controllers;

use App\Interfaces\INotificationRepository;
use App\Mail\NotificationMail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class NotificationController extends Controller
{
    private INotificationRepository $notificationRepository;

    public function __construct(INotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    public function index(): JsonResponse
    {
        $notifications = $this->notificationRepository->getAll();
        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'type' => 'required|in:rendezvous,consultation,resultat,information,rappel',
            'message' => 'required|string'
        ]);

        try {
            $notification = $this->notificationRepository->create($request->all());
            $notification->load('patient.user');
            
            Mail::to($notification->patient->user->email)->send(new NotificationMail($notification));
            $this->notificationRepository->updateDeliveryStatus($notification->id, true, now());
            
            return response()->json([
                'success' => true,
                'message' => 'Notification envoyée avec succès',
                'data' => $notification
            ], 201);
        } catch (\Exception $e) {
            $this->notificationRepository->updateDeliveryStatus($notification->id ?? 0, false, null);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi de la notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $success = $this->notificationRepository->delete($id);
            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notification supprimée avec succès'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification non trouvée'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
