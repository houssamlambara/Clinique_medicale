<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Secrétaire</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <i class="fas fa-user-tie text-purple-600 text-2xl mr-3"></i>
                    <h1 class="text-2xl font-bold text-gray-900">Dashboard Secrétaire</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700" id="userName">Chargement...</span>
                    <button onclick="logout()" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
                        <i class="fas fa-sign-out-alt mr-2"></i>Déconnexion
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">

            <!-- Actions Rapides -->
            <div>
                <h2 class="text-lg font-medium text-gray-900 mb-4">Actions Rapides</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <a href="/secretaire/rendezvous" class="bg-blue-500 text-white p-6 rounded-lg hover:bg-blue-600 transition duration-200 text-center">
                        <i class="fas fa-calendar-plus text-3xl mb-3"></i>
                        <div class="text-lg font-semibold">Gestion des Rendez-vous</div>
                        <div class="text-sm opacity-90 mt-1">Planifier et gérer les rendez-vous médicaux</div>
                    </a>
                    
                    <a href="/patients" class="bg-green-500 text-white p-6 rounded-lg hover:bg-green-600 transition duration-200 text-center">
                        <i class="fas fa-users text-3xl mb-3"></i>
                        <div class="text-lg font-semibold">Gestion des Patients</div>
                        <div class="text-sm opacity-90 mt-1">Consulter et gérer les dossiers patients</div>
                    </a>
                    
                    <a href="/medecin/dashboard" class="bg-purple-500 text-white p-6 rounded-lg hover:bg-purple-600 transition duration-200 text-center">
                        <i class="fas fa-user-md text-3xl mb-3"></i>
                        <div class="text-lg font-semibold">Espace Médecins</div>
                        <div class="text-sm opacity-90 mt-1">Accéder aux informations des médecins</div>
                    </a>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Vérifier l'authentification au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            const token = localStorage.getItem('auth_token');
            const userData = localStorage.getItem('user_data');
            
            if (!token || !userData) {
                // Pas de token, rediriger vers login
                window.location.href = '/login';
                return;
            }
            
            try {
                const user = JSON.parse(userData);
                
                // Afficher le nom de l'utilisateur
                document.getElementById('userName').textContent = user.prenom + ' ' + user.nom;
                
                // Vérifier que l'utilisateur est bien une secrétaire
                if (user.role !== 'secretaire') {
                    alert('Accès non autorisé. Vous devez être une secrétaire.');
                    logout();
                    return;
                }
                
            } catch (error) {
                console.error('Erreur lors du parsing des données utilisateur:', error);
                logout();
            }
        });
        
        function logout() {
            // Supprimer les données d'authentification
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user_data');
            
            // Rediriger vers la page de login
            window.location.href = '/login';
        }
    </script>
</body>
</html> 