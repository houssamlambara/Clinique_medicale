<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\IPatientRepository;
use App\Interfaces\IDossierMedicalRepository;
use App\Interfaces\IPrescriptionRepository;
use App\Interfaces\IRendezvousRepository;
use App\Interfaces\IMedecinRepository;
use App\Interfaces\IMaterielRepository;
use App\Interfaces\IConsultationRepository;
use App\Interfaces\IFactureRepository;
use App\Interfaces\IDepenseRepository;
use App\Interfaces\INotificationRepository;
use App\Repositories\PatientRepository;
use App\Repositories\DossierMedicalRepository;
use App\Repositories\PrescriptionRepository;
use App\Repositories\RendezvousRepository;
use App\Repositories\MedecinRepository;
use App\Repositories\MaterielRepository;
use App\Repositories\ConsultationRepository;
use App\Repositories\FactureRepository;
use App\Repositories\DepenseRepository;
use App\Repositories\NotificationRepository;

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
        $this->app->bind(IMaterielRepository::class, MaterielRepository::class);
        $this->app->bind(IConsultationRepository::class, ConsultationRepository::class);
        $this->app->bind(IFactureRepository::class, FactureRepository::class);
        $this->app->bind(IDepenseRepository::class, DepenseRepository::class);
        $this->app->bind(INotificationRepository::class, NotificationRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
