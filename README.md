<div align="center">

# 🏥 Clinique Médicale

### Système de gestion complet pour cliniques médicales

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg?style=for-the-badge)](LICENSE)

</div>

---

## 📋 À propos du projet

**Clinique Médicale** est une application web moderne et complète conçue pour digitaliser et optimiser la gestion quotidienne d'une clinique médicale. Développée avec Laravel 12 et une architecture Repository Pattern, elle offre une solution robuste, évolutive et sécurisée pour gérer patients, médecins, consultations, rendez-vous, facturation et bien plus encore.

### ✨ Points forts

- 🔐 **Système multi-rôles** : Patient, Médecin, Secrétaire, Comptable
- 🏗️ **Architecture propre** : Repository Pattern avec injection de dépendances
- 🔄 **API RESTful complète** : Toutes les entités accessibles via API
- 📧 **Notifications email** : Rappels automatiques et alertes
- 💳 **Gestion financière** : Factures, paiements, dépenses
- 📊 **Tableaux de bord dédiés** : Interface personnalisée par rôle

---

## 🚀 Fonctionnalités

### 👥 Gestion des utilisateurs
- Authentification sécurisée avec Laravel Sanctum
- Système de rôles et permissions (Patient, Médecin, Secrétaire, Comptable)
- Profils utilisateurs personnalisés par rôle

### 🩺 Gestion médicale
- **Patients** : CRUD complet, dossier médical, historique des consultations
- **Médecins** : Gestion des spécialités, numéro de licence, disponibilités
- **Consultations** : Création, suivi du statut (en cours, terminée, annulée)
- **Dossiers médicaux** : Notes, antécédents, prescriptions associées
- **Prescriptions** : Gestion des médicaments et traitements

### 📅 Gestion des rendez-vous
- Prise de rendez-vous avec créneau horaire
- Vérification des disponibilités
- Notifications automatiques
- Association patient-médecin

### 💰 Gestion financière
- **Factures** : Génération automatique, suivi des paiements
- **Dépenses** : Catégorisation, suivi des paiements
- **Matériels** : Inventaire et gestion du stock

### 📧 Notifications
- Envoi d'emails automatiques
- Rappels de rendez-vous
- Notifications par type (rendez-vous, consultation, résultats, information)

### 📊 Rapports et statistiques
- Génération de rapports médicaux
- Tableau de bord avec indicateurs clés

---

## 🛠️ Technologies utilisées

### Backend
- **Framework** : Laravel 12
- **Langage** : PHP 8.2+
- **ORM** : Eloquent
- **Authentification** : Laravel Sanctum (API tokens)
- **Architecture** : Repository Pattern + Dependency Injection

### Frontend
- **Build tool** : Vite
- **CSS Framework** : Tailwind CSS 4.0
- **Templating** : Blade
- **HTTP Client** : Axios

### Base de données
- Support MySQL/PostgreSQL/SQLite
- Migrations complètes
- Seeders pour données de test

### Outils de développement
- **Tests** : PHPUnit, Faker
- **Code Quality** : Laravel Pint
- **Container** : Laravel Sail (Docker)
- **Debugging** : Laravel Collision

---

## 📁 Architecture du projet

```
Clinique_medicale/
├── app/
│   ├── Http/
│   │   └── Controllers/        # Contrôleurs (Auth, Patient, Médecin, etc.)
│   ├── Models/                 # Modèles Eloquent (User, Patient, Médecin, etc.)
│   ├── Repositories/           # Implémentations des repositories
│   ├── Interfaces/             # Interfaces des repositories
│   ├── Mail/                   # Classes Mailable pour les emails
│   └── Providers/              # Service Providers (injection de dépendances)
├── database/
│   ├── migrations/             # Migrations de base de données
│   └── seeders/                # Seeders pour données de test
├── resources/
│   ├── views/
│   │   ├── patient/            # Vues pour les patients
│   │   ├── medecin/            # Vues pour les médecins
│   │   ├── secretaire/         # Vues pour les secrétaires
│   │   ├── comptable/          # Vues pour les comptables
│   │   └── emails/             # Templates d'emails
│   └── css/                    # Styles CSS/Tailwind
├── routes/
│   ├── web.php                 # Routes web
│   ├── api.php                 # Routes API
│   └── console.php             # Commandes Artisan
└── tests/                      # Tests unitaires et fonctionnels
```

---

## 🔧 Installation

### Prérequis

- PHP >= 8.2
- Composer
- Node.js >= 18.x
- npm ou yarn
- MySQL/PostgreSQL/SQLite

### Étapes d'installation

1. **Cloner le dépôt**
   ```bash
   git clone https://github.com/houssamlambara/Clinique_medicale.git
   cd Clinique_medicale
   ```

2. **Installer les dépendances PHP**
   ```bash
   composer install
   ```

3. **Installer les dépendances JavaScript**
   ```bash
   npm install
   ```

4. **Configurer l'environnement**
   ```bash
   cp .env.example .env
   ```
   
   Modifiez le fichier `.env` avec vos paramètres de base de données :
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=clinique_medicale
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Générer la clé d'application**
   ```bash
   php artisan key:generate
   ```

6. **Exécuter les migrations**
   ```bash
   php artisan migrate
   ```

7. **[Optionnel] Peupler la base de données**
   ```bash
   php artisan db:seed
   ```

8. **Démarrer le serveur de développement**
   
   Terminal 1 - Backend Laravel :
   ```bash
   php artisan serve
   ```
   
   Terminal 2 - Frontend Vite :
   ```bash
   npm run dev
   ```

9. **Accéder à l'application**
   
   Ouvrez votre navigateur à l'adresse : [http://localhost:8000](http://localhost:8000)

---

## 🔌 API REST

L'application expose une API RESTful complète protégée par Laravel Sanctum. Toutes les routes API nécessitent une authentification via token.

### Authentification

```bash
# Inscription
POST /api/auth/register
Content-Type: application/json
{
  "nom": "Dupont",
  "prenom": "Jean",
  "email": "jean.dupont@example.com",
  "password": "password123",
  "telephone": "0612345678",
  "role": "patient",
  "date_naissance": "1990-01-01",
  "genre": "Homme"
}

# Connexion
POST /api/auth/login
Content-Type: application/json
{
  "email": "jean.dupont@example.com",
  "password": "password123"
}

# Déconnexion
POST /api/auth/logout
Authorization: Bearer {token}
```

### Endpoints principaux

| Ressource | Méthode | Endpoint | Description |
|-----------|---------|----------|-------------|
| **Patients** | GET | `/api/patients` | Liste des patients |
| | GET | `/api/patients/{id}` | Détails d'un patient |
| | PUT | `/api/patients/{id}` | Modifier un patient |
| | DELETE | `/api/patients/{id}` | Supprimer un patient |
| **Médecins** | GET | `/api/medecins` | Liste des médecins |
| | GET | `/api/medecins/{id}` | Détails d'un médecin |
| | GET | `/api/medecins/specialite/{specialite}` | Médecins par spécialité |
| **Rendez-vous** | GET | `/api/rendezvous` | Liste des rendez-vous |
| | POST | `/api/rendezvous` | Créer un rendez-vous |
| | PUT | `/api/rendezvous/{id}` | Modifier un rendez-vous |
| | DELETE | `/api/rendezvous/{id}` | Annuler un rendez-vous |
| | GET | `/api/rendezvous/patient/{id}` | RDV d'un patient |
| | GET | `/api/rendezvous/medecin/{id}` | RDV d'un médecin |
| **Consultations** | GET | `/api/consultations` | Liste des consultations |
| | POST | `/api/consultations` | Créer une consultation |
| | GET | `/api/consultations/{id}` | Détails d'une consultation |
| | GET | `/api/consultations/statut/{statut}` | Par statut |
| **Factures** | GET | `/api/factures` | Liste des factures |
| | POST | `/api/factures` | Créer une facture |
| | POST | `/api/factures/{id}/payer` | Marquer comme payée |
| | GET | `/api/factures/non-payer` | Factures impayées |
| **Dossiers médicaux** | GET | `/api/dossiers` | Liste des dossiers |
| | POST | `/api/dossiers` | Créer un dossier |
| | GET | `/api/dossiers/patient/{id}` | Dossier d'un patient |
| **Prescriptions** | POST | `/api/prescriptions` | Créer une prescription |
| | GET | `/api/prescriptions/patient/{id}` | Prescriptions d'un patient |
| **Notifications** | GET | `/api/notifications` | Liste des notifications |
| | POST | `/api/notifications` | Créer une notification |

### Exemple de réponse

```json
{
  "id": 1,
  "nom": "Dupont",
  "prenom": "Jean",
  "email": "jean.dupont@example.com",
  "telephone": "0612345678",
  "role": "patient",
  "patient": {
    "id": 1,
    "date_naissance": "1990-01-01",
    "genre": "Homme"
  }
}
```

---

## 🎨 Interfaces utilisateurs

L'application propose des tableaux de bord personnalisés pour chaque rôle :

### 👤 Patient
- Consulter son dossier médical
- Prendre et gérer ses rendez-vous
- Voir ses consultations passées
- Accéder à ses prescriptions
- Recevoir des notifications

### 👨‍⚕️ Médecin
- Tableau de bord avec rendez-vous du jour
- Gérer ses consultations
- Créer des dossiers médicaux
- Rédiger des prescriptions
- Voir la liste de ses patients

### 👩‍💼 Secrétaire
- Gérer les rendez-vous de tous les médecins
- Envoyer des notifications aux patients
- Gérer l'accueil et les prises de rendez-vous

### 💼 Comptable
- Gérer les factures et paiements
- Suivre les dépenses
- Tableau de bord financier

---

## 🧪 Tests

Le projet inclut une suite de tests pour garantir la qualité du code.

```bash
# Lancer tous les tests
php artisan test

# Tests avec couverture
php artisan test --coverage

# Tests d'un fichier spécifique
php artisan test tests/Feature/PatientTest.php
```

---

## 🤝 Contribution

Les contributions sont les bienvenues ! Voici comment participer :

1. **Fork** le projet
2. **Créez** une branche pour votre fonctionnalité (`git checkout -b feature/AmazingFeature`)
3. **Committez** vos changements (`git commit -m 'Add some AmazingFeature'`)
4. **Pushez** vers la branche (`git push origin feature/AmazingFeature`)
5. **Ouvrez** une Pull Request

### Guidelines de contribution

- Suivez les conventions de code Laravel
- Ajoutez des tests pour les nouvelles fonctionnalités
- Mettez à jour la documentation si nécessaire
- Utilisez des messages de commit clairs et descriptifs

---

## 📝 Licence

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

---

## 📧 Contact

**Houssam Lambara** - [@houssamlambara](https://github.com/houssamlambara)

Lien du projet : [https://github.com/houssamlambara/Clinique_medicale](https://github.com/houssamlambara/Clinique_medicale)

---

<div align="center">

**Fait avec ❤️ pour faciliter la gestion des cliniques médicales**

⭐ N'oubliez pas de mettre une étoile si ce projet vous a été utile !

</div>

<!-- Ancienne section à supprimer -->
<!-- Gestion des utilisateurs (patients, médecins, secrétaire, comptable) avec rôles et authentification
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
