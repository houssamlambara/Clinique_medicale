<?php

namespace App\Repositories;

use App\Interfaces\IMaterielRepository;
use App\Models\Materiels;
use Illuminate\Database\Eloquent\Collection;

class MaterielRepository implements IMaterielRepository
{
    public function getAll(): Collection
    {
        return Materiels::all();
    }

    public function findById(int $id): ?Materiels
    {
        return Materiels::find($id);
    }

    public function create(array $data): Materiels
    {
        return Materiels::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $materiel = Materiels::find($id);
        if (!$materiel) {
            return false;
        }
        return $materiel->update($data);
    }

    public function delete(int $id): bool
    {
        $materiel = Materiels::find($id);
        if (!$materiel) {
            return false;
        }
        return $materiel->delete();
    }

    public function getByNom(string $nom): Collection
    {
        return Materiels::where('nom', 'LIKE', "%{$nom}%")->get();
    }

    public function getByDescription(string $description): Collection
    {
        return Materiels::where('description', 'LIKE', "%{$description}%")->get();
    }

} 