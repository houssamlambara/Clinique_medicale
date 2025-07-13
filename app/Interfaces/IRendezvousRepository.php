<?php

namespace App\Interfaces;

use App\Models\Rendezvous;
use Illuminate\Database\Eloquent\Collection;

interface IRendezvousRepository
{
    public function getAll(): Collection;
    public function findById(int $id): ?Rendezvous;
    public function create(array $data): Rendezvous;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function getByPatientId(int $patientId): Collection;
    public function getByMedecinId(int $medecinId): Collection;
} 