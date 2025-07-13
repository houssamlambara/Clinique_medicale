<?php

namespace App\Repositories;

use App\Interfaces\IMedecinRepository;
use App\Models\Medecin;
use Illuminate\Database\Eloquent\Collection;

class MedecinRepository implements IMedecinRepository
{
    public function getAll(): Collection
    {
        return Medecin::with(['user'])->get();
    }

    public function findById(int $id): ?Medecin
    {
        return Medecin::with(['user'])->find($id);
    }

    public function create(array $data): Medecin
    {
        return Medecin::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $medecin = Medecin::find($id);
        if (!$medecin) {
            return false;
        }
        return $medecin->update($data);
    }

    public function delete(int $id): bool
    {
        $medecin = Medecin::find($id);
        if (!$medecin) {
            return false;
        }
        return $medecin->delete();
    }

    public function getBySpecialite(string $specialite): Collection
    {
        return Medecin::where('specialite', 'LIKE', "%{$specialite}%")
            ->with(['user'])
            ->get();
    }

    // public function search(string $query): Collection
    // {
    //     return Medecin::whereHas('user', function ($q) use ($query) {
    //         $q->where('nom', 'LIKE', "%{$query}%")
    //           ->orWhere('prenom', 'LIKE', "%{$query}%")
    //           ->orWhere('email', 'LIKE', "%{$query}%");
    //     })
    //     ->orWhere('specialite', 'LIKE', "%{$query}%")
    //     ->orWhere('numero_licence', 'LIKE', "%{$query}%")
    //     ->with(['user'])
    //     ->get();
    // }
} 