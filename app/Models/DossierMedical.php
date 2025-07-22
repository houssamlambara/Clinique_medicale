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
        // 'groupe_sanguin',
        // 'antecedents',
        // 'allergies',
        'note',
    ];
    
    //  DossierMedical gère seulement ses relations)
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }


}
