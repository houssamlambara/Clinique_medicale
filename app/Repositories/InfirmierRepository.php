<?php

namespace App\Repositories;

use App\Interfaces\IInfirmierRepository;
use App\Models\Infirmiers;
use Illuminate\Database\Eloquent\Collection;

class InfirmierRepository implements IInfirmierRepository
{
    public function getAll(): Collection
    {
        return Infirmiers::with(['user'])->get();
    }

    public function findById(int $id): ?Infirmiers
    {
        return Infirmiers::with(['user'])->find($id);
    }

    public function create(array $data): Infirmiers
    {
        return Infirmiers::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $infirmier = Infirmiers::find($id);
        if (!$infirmier) {
            return false;
        }
        return $infirmier->update($data);
    }

    public function delete(int $id): bool
    {
        $infirmier = Infirmiers::find($id);
        if (!$infirmier) {
            return false;
        }
        return $infirmier->delete();
    }

    public function getBySpecialite(string $specialite): Collection
    {
        return Infirmiers::where('specialite', 'LIKE', "%{$specialite}%")
            ->with(['user'])
            ->get();
    }
} 