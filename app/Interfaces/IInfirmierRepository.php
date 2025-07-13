<?php

namespace App\Interfaces;

use App\Models\Infirmiers;
use Illuminate\Database\Eloquent\Collection;

interface IInfirmierRepository
{
    public function getAll(): Collection;
    public function findById(int $id): ?Infirmiers;
    public function create(array $data): Infirmiers;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function getBySpecialite(string $specialite): Collection;
} 