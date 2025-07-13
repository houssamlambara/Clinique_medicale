<?php

namespace App\Models;

use App\Interfaces\IPayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class facture extends Model implements IPayable
{
    protected $fillable = [
        'consultation_id',
        'date_facture',
        'montant',
        'est_paye',
        'date_paiement'
    ];

    /**
     * Relation vers Consultation
     */
    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultations::class);
    }

    /**
     * Implémentation de IPayable
     */
    public function getMontant(): float
    {
        return (float) $this->montant;
    }

    public function getDescription(): string
    {
        return "Facture #{$this->id} - Consultation {$this->consultation->motif}";
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

    /**
     * Scopes
     */
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
        return $query->whereDate('date_facture', $date);
    }

}
