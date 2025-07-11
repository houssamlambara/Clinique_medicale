<?php

namespace App\Interfaces;

use App\Models\DossierMedical;
use Illuminate\Database\Eloquent\Collection;

interface IDossierMedicalRepository
{
    public function getByPatientId(int $patientId): ?DossierMedical;
    public function create(array $data): DossierMedical;
    public function update(int $id, array $data): bool;

} 