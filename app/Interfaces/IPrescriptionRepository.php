<?php

namespace App\Interfaces;

use App\Models\Prescription;
use Illuminate\Database\Eloquent\Collection;

interface IPrescriptionRepository
{
    public function getByPatientId(int $patientId): Collection;
    public function create(array $data): Prescription;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function findById(int $id): ?Prescription;
    public function getAll(): Collection;
} 