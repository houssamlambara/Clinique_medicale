<?php

namespace App\Repositories;

use App\Interfaces\INotificationRepository;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Collection;

class NotificationRepository implements INotificationRepository
{
    public function getAll(): Collection
    {
        return Notification::with('patient.user')->orderBy('created_at', 'desc')->get();
    }

    public function create(array $data): Notification
    {
        return Notification::create($data);
    }

    public function updateDeliveryStatus(int $id, bool $emailSent, ?string $sentAt = null): bool
    {
        $notification = Notification::find($id);
        if (!$notification) {
            return false;
        }
        
        return $notification->update([
            'email_sent' => $emailSent,
            'sent_at' => $sentAt
        ]);
    }

    public function delete(int $id): bool
    {
        $notification = Notification::find($id);
        if (!$notification) {
            return false;
        }
        return $notification->delete();
    }
} 