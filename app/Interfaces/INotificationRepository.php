<?php

namespace App\Interfaces;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Collection;

interface INotificationRepository
{
    public function getAll(): Collection;
    public function create(array $data): Notification;
    public function updateDeliveryStatus(int $id, bool $emailSent, ?string $sentAt = null): bool;
    public function delete(int $id): bool;
} 