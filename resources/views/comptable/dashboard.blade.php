<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Comptable - Clinique Médicale</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center space-x-4">
                        <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-3 rounded-xl">
                            <i class="fas fa-calculator text-white text-2xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Dashboard Comptable</h1>
                            <p class="text-sm text-gray-600">Gestion financière de la clinique</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-600" id="comptable-name">Comptable</span>
                        <button onclick="logout()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-sign-out-alt mr-2"></i>Déconnexion
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Actions Rapides -->
            <div class="mb-8">
                <h2 class="text-lg font-bold mb-4 text-gray-900">Actions Rapides</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <a href="/comptable/factures" class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-xl text-center hover:from-blue-600 hover:to-blue-700 transition-all duration-200 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-file-invoice text-3xl mb-3"></i>
                        <div class="font-semibold text-lg">Gérer les Factures</div>
                        <p class="text-sm opacity-90 mt-1">Créer et gérer les factures</p>
                    </a>

                    <a href="/comptable/depenses" class="bg-gradient-to-r from-red-500 to-red-600 text-white p-6 rounded-xl text-center hover:from-red-600 hover:to-red-700 transition-all duration-200 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-money-bill-wave text-3xl mb-3"></i>
                        <div class="font-semibold text-lg">Gérer les Dépenses</div>
                        <p class="text-sm opacity-90 mt-1">Suivre les dépenses de la clinique</p>
                    </a>

                    <a href="/comptable/rapports" class="bg-gradient-to-r from-purple-500 to-purple-600 text-white p-6 rounded-xl text-center hover:from-purple-600 hover:to-purple-700 transition-all duration-200 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-chart-bar text-3xl mb-3"></i>
                        <div class="font-semibold text-lg">Rapports Financiers</div>
                        <p class="text-sm opacity-90 mt-1">Analyser les finances</p>
                    </a>

                    <button onclick="loadNotifications()" class="bg-gradient-to-r from-orange-500 to-orange-600 text-white p-6 rounded-xl text-center hover:from-orange-600 hover:to-orange-700 transition-all duration-200 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-bell text-3xl mb-3"></i>
                        <div class="font-semibold text-lg">Notifications</div>
                        <p class="text-sm opacity-90 mt-1">Voir les alertes financières</p>
                    </button>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Charger les données de l'utilisateur
        document.addEventListener('DOMContentLoaded', function () {
            loadUserData();
        });

        function loadUserData() {
            const userData = localStorage.getItem('user_data');
            
            if (userData) {
                const currentComptable = JSON.parse(userData);
                
                if (currentComptable.role === 'comptable') {
                    document.getElementById('comptable-name').textContent = `${currentComptable.prenom} ${currentComptable.nom}`;
                }
            }
        }

        function logout() {
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user_data');
            window.location.href = '/login';
        }

        function loadNotifications() {
            alert('Fonctionnalité en cours de développement');
        }
    </script>
</body>

</html> 