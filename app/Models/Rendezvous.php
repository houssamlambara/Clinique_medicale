<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rendezvous extends Model
{
    protected $table = "rendezvous";
    
    protected $fillable = [
        'patient_id',
        'medecin_id',
        'date_rdv',
    ];

    /**
     * Relation avec le patient
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Relation avec le médecin
     */
    public function medecin(): BelongsTo
    {
        return $this->belongsTo(Medecin::class);
    }
}
