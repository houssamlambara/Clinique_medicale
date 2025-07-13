<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\IPatientRepository;
use App\Interfaces\IDossierMedicalRepository;
use App\Interfaces\IPrescriptionRepository;
use App\Interfaces\IRendezvousRepository;
use App\Interfaces\IMedecinRepository;
use App\Interfaces\IInfirmierRepository;
use App\Interfaces\IMaterielRepository;
use App\Interfaces\IConsultationRepository;
use App\Repositories\PatientRepository;
use App\Repositories\DossierMedicalRepository;
use App\Repositories\PrescriptionRepository;
use App\Repositories\RendezvousRepository;
use App\Repositories\MedecinRepository;
use App\Repositories\InfirmierRepository;
use App\Repositories\MaterielRepository;
use App\Repositories\ConsultationRepository;

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
        $this->app->bind(IInfirmierRepository::class, InfirmierRepository::class);
        $this->app->bind(IMaterielRepository::class, MaterielRepository::class);
        $this->app->bind(IConsultationRepository::class, ConsultationRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
