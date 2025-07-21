<?php

namespace App\Repositories;

use App\Interfaces\IConsultationRepository;
use App\Models\Consultations;
use Illuminate\Database\Eloquent\Collection;

class ConsultationRepository implements IConsultationRepository
{
    public function getAll(): Collection
    {
        return Consultations::with(['patient.user', 'medecin.user'])->get();
    }

    public function findById(int $id): ?Consultations
    {
        return Consultations::with(['patient.user', 'medecin.user'])->find($id);
    }

    public function create(array $data): Consultations
    {
        return Consultations::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $consultation = Consultations::find($id);
        if (!$consultation) {
            return false;
        }
        return $consultation->update($data);
    }

    public function delete(int $id): bool
    {
        $consultation = Consultations::find($id);
        if (!$consultation) {
            return false;
        }
        return $consultation->delete();
    }

    public function getByPatient(int $patientId): Collection
    {
        return Consultations::with(['patient.user', 'medecin.user'])
            ->where('patient_id', $patientId)
            ->get();
    }

    public function getByMedecin(int $medecinId): Collection
    {
        return Consultations::with(['patient.user', 'medecin.user'])
            ->where('medecin_id', $medecinId)
            ->get();
    }

    public function getByStatut(string $statut): Collection
    {
        return Consultations::with(['patient.user', 'medecin.user'])
            ->where('statut', $statut)
            ->get();
    }

} 