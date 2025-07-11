<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DossierMedical extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'groupe_sanguin',
        'antecedents',
        'allergies',
        'notes_medicales',
    ];

    protected $casts = [
        'antecedents' => 'array',
        'allergies' => 'array',
    ];

    // Relations (SRP : DossierMedical gère seulement ses relations)
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    // Méthodes utilitaires
    public function addAntecedent(string $antecedent): void
    {
        $antecedents = $this->antecedents ?? [];
        $antecedents[] = [
            'antecedent' => $antecedent,
            'date_ajout' => now()->toDateString()
        ];
        $this->update(['antecedents' => $antecedents]);
    }

    public function addAllergie(string $allergie): void
    {
        $allergies = $this->allergies ?? [];
        $allergies[] = [
            'allergie' => $allergie,
            'date_ajout' => now()->toDateString()
        ];
        $this->update(['allergies' => $allergies]);
    }

    public function getAntecedentsListAttribute(): array
    {
        return collect($this->antecedents ?? [])->pluck('antecedent')->toArray();
    }

    public function getAllergiesListAttribute(): array
    {
        return collect($this->allergies ?? [])->pluck('allergie')->toArray();
    }
}
