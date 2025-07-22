<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Infirmiers extends Model
{
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

   
    public function scopeBySpecialite($query, string $specialite)
    {
        return $query->where('specialite', 'LIKE', "%{$specialite}%");
    }

    
    public function scopeByLicence($query, string $numeroLicence)
    {
        return $query->where('numero_licence', 'LIKE', "%{$numeroLicence}%");
    }

}
