<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Consultations extends Model
{
    protected $fillable = [
        'patient_id',
        'medecin_id',
        'montant',
        'motif',
        'statut'
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function medecin(): BelongsTo
    {
        return $this->belongsTo(Medecin::class);
    }

    public function factures(): HasMany
    {
        return $this->hasMany(facture::class);
    }

    public function scopeEnCours($query)
    {
        return $query->where('statut', 'en_cours');
    }

    public function scopeTerminees($query)
    {
        return $query->where('statut', 'terminée');
    }

    public function scopeAnnulees($query)
    {
        return $query->where('statut', 'annulée');
    }

    public function scopeByMotif($query, string $motif)
    {
        return $query->where('motif', 'LIKE', "%{$motif}%");
    }

    public function scopeByMontantMin($query, float $montant)
    {
        return $query->where('montant', '>=', $montant);
    }

    public function scopeByMontantMax($query, float $montant)
    {
        return $query->where('montant', '<=', $montant);
    }
}
