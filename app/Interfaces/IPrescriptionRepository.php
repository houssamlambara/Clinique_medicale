<?php

namespace App\Interfaces;

use App\Models\Prescription;
use Illuminate\Database\Eloquent\Collection;

interface IPrescriptionRepository
{
    
    public function create(array $data): Prescription;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function getPrescriptionsByMedecin(int $medecinId): Collection;
    public function getPrescriptionsByDossier(int $dossierId): Collection;
} 