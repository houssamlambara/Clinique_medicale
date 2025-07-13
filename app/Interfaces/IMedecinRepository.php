<?php

namespace App\Interfaces;

use App\Models\Medecin;
use Illuminate\Database\Eloquent\Collection;

interface IMedecinRepository
{
    public function getAll(): Collection;
    public function findById(int $id): ?Medecin;
    public function create(array $data): Medecin;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function getBySpecialite(string $specialite): Collection;
    // public function search(string $query): Collection;
} 