<?php

namespace App\Repositories;

use App\Interfaces\IPatientRepository;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Collection;

class PatientRepository implements IPatientRepository
{
    public function getAll(): Collection
    {
        return Patient::with('user')->get();
    }

    public function findById(int $id): ?Patient
    {
        return Patient::with('user', 'dossierMedical')->find($id);
    }

    public function findByUserId(int $userId): ?Patient
    {
        return Patient::where('user_id', $userId)->with('user')->first();
    }

    public function create(array $data): Patient
    {
        return Patient::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $patient = Patient::find($id);
        if (!$patient) {
            return false;
        }
        return $patient->update($data);
    }

    public function delete(int $id): bool
    {
        $patient = Patient::find($id);
        if (!$patient) {
            return false;
        }
        return $patient->delete();
    }

    public function search(string $query): Collection
    {
        return Patient::with('user')
            ->whereHas('user', function ($q) use ($query) {
                $q->where('nom', 'like', "%{$query}%")
                  ->orWhere('prenom', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->get();
    }
} 