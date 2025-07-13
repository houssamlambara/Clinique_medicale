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

    /**
     * Relation vers Patient
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Relation vers Medecin
     */
    public function medecin(): BelongsTo
    {
        return $this->belongsTo(Medecin::class);
    }

    /**
     * Relation vers Facture
     */
    public function factures(): HasMany
    {
        return $this->hasMany(facture::class);
    }

    /**
     * Scope pour les consultations en cours
     */
    public function scopeEnCours($query)
    {
        return $query->where('statut', 'en_cours');
    }

    /**
     * Scope pour les consultations terminées
     */
    public function scopeTerminees($query)
    {
        return $query->where('statut', 'terminée');
    }

    /**
     * Scope pour les consultations annulées
     */
    public function scopeAnnulees($query)
    {
        return $query->where('statut', 'annulée');
    }

    /**
     * Scope pour rechercher par motif
     */
    public function scopeByMotif($query, string $motif)
    {
        return $query->where('motif', 'LIKE', "%{$motif}%");
    }

    /**
     * Scope pour les consultations par montant minimum
     */
    public function scopeByMontantMin($query, float $montant)
    {
        return $query->where('montant', '>=', $montant);
    }

    /**
     * Scope pour les consultations par montant maximum
     */
    public function scopeByMontantMax($query, float $montant)
    {
        return $query->where('montant', '<=', $montant);
    }
}
