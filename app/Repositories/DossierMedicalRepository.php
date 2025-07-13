<?php

namespace App\Repositories;

use App\Interfaces\IDossierMedicalRepository;
use App\Models\DossierMedical;
use Illuminate\Database\Eloquent\Collection;

class DossierMedicalRepository implements IDossierMedicalRepository
{
    /**
     * Relations à charger par défaut
     */
    private function getDefaultRelations(): array
    {
        return ['patient.user', 'prescriptions'];
    }

    public function getAll(): Collection
    {
        return DossierMedical::with($this->getDefaultRelations())->get();
    }

    public function findById(int $id): ?DossierMedical
    {
        return DossierMedical::with($this->getDefaultRelations())->find($id);
    }

    public function getByPatientId(int $patientId): ?DossierMedical
    {
        return DossierMedical::where('patient_id', $patientId)
            ->with($this->getDefaultRelations())
            ->first();
    }

    public function create(array $data): DossierMedical
    {
        $dossier = DossierMedical::create($data);
        return $dossier->load($this->getDefaultRelations());
    }

    public function update(int $id, array $data): ?DossierMedical
    {
        $dossier = DossierMedical::find($id);
        if (!$dossier) {
            return null;
        }
        $dossier->update($data);
        return $dossier->fresh($this->getDefaultRelations());
    }
    
    public function delete(int $id): bool
    {
        $dossier = DossierMedical::find($id);
        if (!$dossier) {
            return false;
        }
        return $dossier->delete();
    }
}
