<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date_naissance',
        'genre'
    ];

    protected $casts = [
        'date_naissance' => 'date'
    ];

    // Relation vers User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relations principales
    public function dossiersMedicaux()
    {
        return $this->hasMany(DossierMedical::class);
    }

    public function rendezVous()
    {
        return $this->hasMany(Rendezvous::class);
    }
}
