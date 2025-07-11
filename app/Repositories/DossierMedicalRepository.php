<?php

namespace App\Repositories;

use App\Interfaces\IDossierMedicalRepository;
use App\Models\DossierMedical;
use Illuminate\Database\Eloquent\Collection;

class DossierMedicalRepository implements IDossierMedicalRepository
{
    public function getByPatientId(int $patientId): ?DossierMedical
    {
        return DossierMedical::where('patient_id', $patientId)
            ->with('patient', 'prescriptions')
            ->first();
    }

    public function create(array $data): DossierMedical
    {
        return DossierMedical::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $dossier = DossierMedical::find($id);
        if (!$dossier) {
            return false;
        }
        return $dossier->update($data);
    }

} 