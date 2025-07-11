<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date_naissance',
        'genre',
    ];

    protected $casts = [
        'date_naissance' => 'date',
    ];

    // Relations (SRP : Patient gère seulement ses relations)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dossierMedical(): HasOne
    {
        return $this->hasOne(DossierMedical::class);
    }

    // Méthodes utilitaires
    public function getFullNameAttribute(): string
    {
        return $this->user->prenom . ' ' . $this->user->nom;
    }

    public function getAgeAttribute(): int
    {
        return $this->date_naissance->age;
    }
}
