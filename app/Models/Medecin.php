<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medecin extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'specialite',
        'numero_licence'
    ];

   
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    
    public function consultations(): HasMany
    {
        return $this->hasMany(Consultations::class);
    }

    public function rendezVous(): HasMany
    {
        return $this->hasMany(Rendezvous::class);
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    
    public function scopeBySpecialite($query, string $specialite)
    {
        return $query->where('specialite', 'LIKE', "%{$specialite}%");
    }

  
    public function scopeByLicence($query, string $numeroLicence)
    {
        return $query->where('numero_licence', 'LIKE', "%{$numeroLicence}%");
    }
}
