<?php

namespace App\Interfaces;

use App\Models\Depense;
use Illuminate\Database\Eloquent\Collection;

interface IDepenseRepository
{
    public function getAll(): Collection;
    public function findById(int $id): ?Depense;
    public function create(array $data): Depense;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function getByDate(string $date): Collection;
    public function getNonPayer(): Collection;
    public function getPayer(): Collection;
} 