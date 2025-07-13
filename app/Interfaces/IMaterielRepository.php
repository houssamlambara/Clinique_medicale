<?php

namespace App\Interfaces;

use App\Models\Materiels;
use Illuminate\Database\Eloquent\Collection;

interface IMaterielRepository
{
    public function getAll(): Collection;
    public function findById(int $id): ?Materiels;
    public function create(array $data): Materiels;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function getByNom(string $nom): Collection;
    public function getByDescription(string $description): Collection;
} 