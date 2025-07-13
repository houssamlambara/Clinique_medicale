<?php

namespace App\Interfaces;

use App\Models\Consultations;
use Illuminate\Database\Eloquent\Collection;

interface IConsultationRepository
{
    public function getAll(): Collection;
    public function findById(int $id): ?Consultations;
    public function create(array $data): Consultations;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function getByPatient(int $patientId): Collection;
    public function getByMedecin(int $medecinId): Collection;
    public function getByStatut(string $statut): Collection;
} 