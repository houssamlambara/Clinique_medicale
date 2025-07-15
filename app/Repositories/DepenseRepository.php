<?php

namespace App\Repositories;

use App\Interfaces\IDepenseRepository;
use App\Models\Depense;
use Illuminate\Database\Eloquent\Collection;

class DepenseRepository implements IDepenseRepository
{
    public function getAll(): Collection
    {
        return Depense::all();
    }

    public function findById(int $id): ?Depense
    {
        return Depense::find($id);
    }

    public function create(array $data): Depense
    {
        return Depense::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $depense = Depense::find($id);
        if (!$depense) {
            return false;
        }
        return $depense->update($data);
    }

    public function delete(int $id): bool
    {
        $depense = Depense::find($id);
        if (!$depense) {
            return false;
        }
        return $depense->delete();
    }

    public function getByDate(string $date): Collection
    {
        return Depense::whereDate('date_depense', $date)->get();
    }

    public function getNonPayer(): Collection
    {
        return Depense::where('est_paye', false)->get();
    }

    public function getPayer(): Collection
    {
        return Depense::where('est_paye', true)->get();
    }

} 