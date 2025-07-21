<?php

namespace App\Models;

use App\Interfaces\IPayable;
use Illuminate\Database\Eloquent\Model;

class Depense extends Model implements IPayable
{
    protected $fillable = [
        'date_depense',
        'description',
        'categorie',
        'montant',
        'est_paye',
        'date_paiement'
    ];

    /**
     * Implémentation de IPayable
     */
    public function getMontant(): float
    {
        return (float) $this->montant;
    }

    public function getDescription(): string
    {
        return "Dépense #{$this->id} - {$this->description}";
    }

    public function estPayer(): bool
    {
        return $this->est_paye;
    }

    public function marquerCommePayer(): void
    {
        $this->update([
            'est_paye' => true,
            'date_paiement' => now()
        ]);
    }

    public function getDatePaiement(): ?string
    {
        return $this->date_paiement?->toDateString();
    }

    public function scopeNonPayer($query)
    {
        return $query->where('est_paye', false);
    }

    public function scopePayer($query)
    {
        return $query->where('est_paye', true);
    }

    public function scopeByDate($query, string $date)
    {
        return $query->whereDate('date_depense', $date);
    }
}
