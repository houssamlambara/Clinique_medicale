<?php

namespace App\Repositories;

use App\Interfaces\IPrescriptionRepository;
use App\Models\Prescription;
use Illuminate\Database\Eloquent\Collection;

class PrescriptionRepository implements IPrescriptionRepository
{
    public function getByPatientId(int $patientId): Collection
    {
        return Prescription::whereHas('dossierMedical', function ($query) use ($patientId) {
            $query->where('patient_id', $patientId);
        })->with(['dossierMedical.patient.user'])->get();
    }

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

    public function findById(int $id): ?Prescription
    {
        return Prescription::with(['dossierMedical.patient.user', 'medecin.user'])->find($id);
    }

    public function getAll(): Collection
    {
        return Prescription::with(['dossierMedical.patient.user', 'medecin.user'])->get();
    }
} 