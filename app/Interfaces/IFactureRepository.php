<?php

namespace App\Interfaces;

use App\Models\facture;
use Illuminate\Database\Eloquent\Collection;

interface IFactureRepository
{
    public function getAll(): Collection;
    public function findById(int $id): ?facture;
    public function create(array $data): facture;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function getByConsultation(int $consultationId): Collection;
    public function getByDate(string $date): Collection;
    public function getNonPayer(): Collection;
    public function getPayer(): Collection;
} 