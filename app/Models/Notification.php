<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'type',
        'message',
        'email_sent',
        'sent_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'sent_at' => 'datetime',
        'email_sent' => 'boolean'
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function getTypeLabel(): string
    {
        return match($this->type) {
            'rendezvous' => 'Rendez-vous',
            'consultation' => 'Consultation',
            'resultat' => 'Résultat',
            'information' => 'Information',
            'rappel' => 'Rappel',
            default => 'Notification'
        };
    }

    public function getTypeIcon(): string
    {
        return match($this->type) {
            'rendezvous' => 'fas fa-calendar-check',
            'consultation' => 'fas fa-stethoscope',
            'resultat' => 'fas fa-file-medical',
            'information' => 'fas fa-info-circle',
            'rappel' => 'fas fa-bell',
            default => 'fas fa-bell'
        };
    }

    public function getTypeColor(): string
    {
        return match($this->type) {
            'rendezvous' => 'blue',
            'consultation' => 'green',
            'resultat' => 'purple',
            'information' => 'gray',
            'rappel' => 'orange',
            default => 'gray'
        };
    }
}
