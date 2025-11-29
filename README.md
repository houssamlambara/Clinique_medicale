<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**

# 🏥 Clinique Médicale – Application de gestion

![Laravel Logo](https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg)

## Présentation

Ce projet est une application web complète pour la gestion d’une clinique médicale. Elle permet d’administrer les patients, médecins, rendez-vous, consultations, dossiers médicaux, prescriptions, factures, dépenses, matériels, notifications et rapports, avec une interface dédiée pour chaque rôle utilisateur.

## Fonctionnalités

- Gestion des utilisateurs (patients, médecins, secrétaire, comptable) avec rôles et authentification
- Gestion des patients (CRUD, dossier médical, historique)
- Gestion des médecins (spécialités, licence, consultations)
- Prise et gestion des rendez-vous
- Gestion des consultations (motif, statut, montant)
- Dossiers médicaux et prescriptions
- Facturation et suivi des paiements
- Gestion des dépenses et matériels
- Notifications (email, rappels)
- Rapports médicaux
- API RESTful pour toutes les entités
- Vues web dédiées pour chaque rôle

## Technologies

- **Backend** : Laravel 12, PHP 8.2+, Eloquent ORM
- **Authentification API** : Laravel Sanctum
- **Frontend** : Vite, Tailwind CSS, Axios
- **Tests** : PHPUnit, Faker
- **Outils dev** : Laravel Sail, Pint, Collision, Mockery

## Structure du projet

- `app/Models` : Modèles Eloquent
- `app/Http/Controllers` : Contrôleurs
- `app/Repositories` : Repositories pour la logique métier
- `app/Interfaces` : Interfaces des repositories
- `routes/` : Fichiers de routes web et API
- `resources/views` : Vues Blade pour chaque rôle
- `database/migrations` : Migrations pour la base de données
- `tests/` : Tests unitaires et fonctionnels

## Installation

1. Cloner le dépôt :
   ```bash
   git clone https://github.com/houssamlambara/Clinique_medicale.git
   cd Clinique_medicale
   ```
2. Installer les dépendances PHP :
   ```bash
   composer install
   ```
3. Installer les dépendances JS :
   ```bash
   npm install
   ```
4. Copier `.env.example` en `.env` et configurer la base de données
5. Générer la clé d’application :
   ```bash
   php artisan key:generate
   ```
6. Lancer les migrations :
   ```bash
   php artisan migrate
   ```
7. (Optionnel) Lancer les seeders :
   ```bash
   php artisan db:seed
   ```
8. Démarrer le serveur Laravel :
   ```bash
   php artisan serve
   ```
9. Démarrer le frontend :
   ```bash
   npm run dev
   ```

## Utilisation

Accédez à l’application sur [http://localhost:8000](http://localhost:8000) après avoir démarré le serveur. Utilisez les interfaces selon votre rôle (patient, médecin, secrétaire, comptable).

## API

L’API REST est définie dans `routes/api.php` et couvre toutes les entités : patients, médecins, consultations, rendez-vous, dossiers médicaux, prescriptions, factures, dépenses, matériels, notifications, rapports.

## Tests

Pour lancer les tests :
```bash
php artisan test
```

## Contribution

Les contributions sont les bienvenues ! Ouvrez une issue ou une pull request pour proposer des améliorations.

## Licence

Ce projet est sous licence MIT.
