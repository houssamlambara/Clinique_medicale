<?php

namespace App\Repositories;

use App\Interfaces\IFactureRepository;
use App\Models\facture;
use Illuminate\Database\Eloquent\Collection;

class FactureRepository implements IFactureRepository
{
    public function getAll(): Collection
    {
        return facture::with(['consultation.patient', 'consultation.medecin'])->get();
    }

    public function findById(int $id): ?facture
    {
        return facture::with(['consultation.patient', 'consultation.medecin'])->find($id);
    }

    public function create(array $data): facture
    {
        return facture::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $facture = facture::find($id);
        if (!$facture) {
            return false;
        }
        return $facture->update($data);
    }

    public function delete(int $id): bool
    {
        $facture = facture::find($id);
        if (!$facture) {
            return false;
        }
        return $facture->delete();
    }

    public function getByConsultation(int $consultationId): Collection
    {
        return facture::with(['consultation.patient', 'consultation.medecin'])
            ->where('consultation_id', $consultationId)
            ->get();
    }

    public function getByDate(string $date): Collection
    {
        return facture::with(['consultation.patient', 'consultation.medecin'])
            ->whereDate('date_facture', $date)
            ->get();
    }

    public function getNonPayer(): Collection
    {
        return facture::with(['consultation.patient', 'consultation.medecin'])
            ->where('est_paye', false)
            ->get();
    }

    public function getPayer(): Collection
    {
        return facture::with(['consultation.patient', 'consultation.medecin'])
            ->where('est_paye', true)
            ->get();
    }
} 