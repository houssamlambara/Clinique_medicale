<?php

namespace App\Interfaces;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Collection;

interface IPatientRepository
{
    public function getAll(): Collection;
    public function findById(int $id): ?Patient;
    public function findByUserId(int $userId): ?Patient;
    public function create(array $data): Patient;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function search(string $query): Collection;
} 