<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    // Patient gère seulement ses relations)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dossierMedical(): HasOne
    {
        return $this->hasOne(DossierMedical::class);
    }

    public function rendezvous(): HasMany
    {
        return $this->hasMany(Rendezvous::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->user->prenom . ' ' . $this->user->nom;
    }

    public function getAgeAttribute(): int
    {
        return $this->date_naissance->age;
    }
}
