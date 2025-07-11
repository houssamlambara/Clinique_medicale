<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\IPatientRepository;
use App\Interfaces\IDossierMedicalRepository;
use App\Repositories\PatientRepository;
use App\Repositories\DossierMedicalRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Injection de dépendance pour les repositories
        $this->app->bind(IPatientRepository::class, PatientRepository::class);
        $this->app->bind(IDossierMedicalRepository::class, DossierMedicalRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
