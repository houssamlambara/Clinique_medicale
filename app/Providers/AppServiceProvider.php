<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\IPatientRepository;
use App\Interfaces\IDossierMedicalRepository;
use App\Interfaces\IPrescriptionRepository;
use App\Interfaces\IRendezvousRepository;
use App\Interfaces\IMedecinRepository;
use App\Interfaces\IInfirmierRepository;
use App\Repositories\PatientRepository;
use App\Repositories\DossierMedicalRepository;
use App\Repositories\PrescriptionRepository;
use App\Repositories\RendezvousRepository;
use App\Repositories\MedecinRepository;
use App\Repositories\InfirmierRepository;

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
        $this->app->bind(IPrescriptionRepository::class, PrescriptionRepository::class);
        $this->app->bind(IRendezvousRepository::class, RendezvousRepository::class);
        $this->app->bind(IMedecinRepository::class, MedecinRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
