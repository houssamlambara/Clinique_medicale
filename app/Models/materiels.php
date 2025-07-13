<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Materiels extends Model
{
    protected $table = 'materiels';

    protected $fillable = [
        'nom',
        'description'
    ];

    /**
     * Relations principales
     */
    public function consultations(): HasMany
    {
        return $this->hasMany(Consultations::class);
    }

    /**
     * Scope pour rechercher par nom
     */
    public function scopeByNom($query, string $nom)
    {
        return $query->where('nom', 'LIKE', "%{$nom}%");
    }

    /**
     * Scope pour rechercher par description
     */
    public function scopeByDescription($query, string $description)
    {
        return $query->where('description', 'LIKE', "%{$description}%");
    }
}
