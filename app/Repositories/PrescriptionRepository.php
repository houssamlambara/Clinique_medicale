<?php

namespace App\Repositories;

use App\Interfaces\IPrescriptionRepository;
use App\Models\Prescription;
use Illuminate\Database\Eloquent\Collection;

class PrescriptionRepository implements IPrescriptionRepository
{

    public function create(array $data): Prescription
    {
        return Prescription::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $prescription = Prescription::find($id);
        if (!$prescription) {
            return false;
        }
        return $prescription->update($data);
    }

    public function delete(int $id): bool
    {
        $prescription = Prescription::find($id);
        if (!$prescription) {
            return false;
        }
        return $prescription->delete();
    }

    public function getPrescriptionsByMedecin(int $medecinId): Collection
    {
        return Prescription::with(['dossierMedical.patient.user', 'medecin.user'])
            ->where('medecin_id', $medecinId)
            ->get();
    }

    public function getPrescriptionsByDossier(int $dossierId): Collection
    {
        return Prescription::with(['dossierMedical.patient.user', 'medecin.user'])
            ->where('dossier_medical_id', $dossierId)
            ->get();
    }
} 