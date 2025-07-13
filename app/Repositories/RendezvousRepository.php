<?php

namespace App\Repositories;

use App\Interfaces\IRendezvousRepository;
use App\Models\Rendezvous;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class RendezvousRepository implements IRendezvousRepository
{
    public function getAll(): Collection
    {
        return Rendezvous::with(['patient.user', 'medecin.user'])->get();
    }

    public function findById(int $id): ?Rendezvous
    {
        return Rendezvous::with(['patient.user', 'medecin.user'])->find($id);
    }

    public function create(array $data): Rendezvous
    {
        return Rendezvous::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $rendezvous = Rendezvous::find($id);
        if (!$rendezvous) {
            return false;
        }
        return $rendezvous->update($data);
    }

    public function delete(int $id): bool
    {
        $rendezvous = Rendezvous::find($id);
        if (!$rendezvous) {
            return false;
        }
        return $rendezvous->delete();
    }

    public function getByPatientId(int $patientId): Collection
    {
        return Rendezvous::where('patient_id', $patientId)
            ->with(['patient.user', 'medecin.user'])
            ->orderBy('date_rdv', 'asc')
            ->get();
    }

    public function getByMedecinId(int $medecinId): Collection
    {
        return Rendezvous::where('medecin_id', $medecinId)
            ->with(['patient.user', 'medecin.user'])
            ->orderBy('date_rdv', 'asc')
            ->get();
    }
} 