<?php

namespace App\Interfaces;

interface IPayable
{
    /**
     * Obtenir le montant à payer
     */
    public function getMontant(): float;

    /**
     * Obtenir la description du paiement
     */
    public function getDescription(): string;

    /**
     * Vérifier si l'élément est payé
     */
    public function estPayer(): bool;

    /**
     * Marquer comme payé
     */
    public function marquerCommePayer(): void;

    /**
     * Obtenir la date de paiement
     */
    public function getDatePaiement(): ?string;
} 