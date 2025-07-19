<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Patient</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-lg border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-3 rounded-xl shadow-lg">
                        <i class="fas fa-user-injured text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Dashboard Patient</h1>
                        <p class="text-sm text-gray-600">Gestion de vos informations médicales</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <span id="userName" class="text-gray-700 font-medium">Chargement...</span>
                    <button onclick="logout()" class="bg-gradient-to-r from-red-500 to-red-600 text-white px-6 py-2 rounded-lg hover:from-red-600 hover:to-red-700 transition-all duration-200 transform hover:scale-105 shadow-md">
                        <i class="fas fa-sign-out-alt mr-2"></i>Déconnexion
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <!-- Actions Rapides -->
        <div class="mb-8">
            <h2 class="text-lg font-bold mb-4 text-gray-900">Actions Rapides</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="/rendezvous" class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-xl text-center hover:from-blue-600 hover:to-blue-700 transition-all duration-200 transform hover:scale-105 shadow-lg">
                    <i class="fas fa-calendar-check text-3xl mb-3"></i>
                    <div class="font-semibold text-lg">Mes Rendez-vous</div>
                    <p class="text-sm opacity-90 mt-1">Gérer mes rendez-vous médicaux</p>
                </a>
                
                <a href="/consultations" class="bg-gradient-to-r from-green-500 to-green-600 text-white p-6 rounded-xl text-center hover:from-green-600 hover:to-green-700 transition-all duration-200 transform hover:scale-105 shadow-lg">
                    <i class="fas fa-stethoscope text-3xl mb-3"></i>
                    <div class="font-semibold text-lg">Mes Consultations</div>
                    <p class="text-sm opacity-90 mt-1">Consulter mes résultats médicaux</p>
                </a>
                
                <button onclick="loadNotifications()" class="bg-gradient-to-r from-purple-500 to-purple-600 text-white p-6 rounded-xl text-center hover:from-purple-600 hover:to-purple-700 transition-all duration-200 transform hover:scale-105 shadow-lg">
                    <i class="fas fa-bell text-3xl mb-3"></i>
                    <div class="font-semibold text-lg">Notifications</div>
                    <p class="text-sm opacity-90 mt-1">Voir mes notifications</p>
                </button>
            </div>
        </div>
    </main>

    <script>
        var currentPatient = null;

        // Charger les données du patient
        function loadPatientData() {
            var userData = localStorage.getItem('user_data');
            if (userData) {
                currentPatient = JSON.parse(userData);
                document.getElementById('userName').textContent = currentPatient.nom + ' ' + currentPatient.prenom;
            } else {
                alert('Aucun utilisateur connecté');
            }
        }

        function logout() {
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user_data');
            window.location.href = '/login';
        }

        // Charger les notifications
        function loadNotifications() {
            if (!currentPatient) return;

            // Simulation de notifications pour l'exemple
            const notifications = [
                {
                    id: 1,
                    type: 'rendez-vous',
                    message: 'Votre rendez-vous du 15 janvier a été confirmé',
                    date: new Date().toLocaleDateString('fr-FR'),
                    icon: 'fas fa-calendar-check',
                    color: 'text-blue-600'
                },
                {
                    id: 2,
                    type: 'consultation',
                    message: 'Vos résultats d\'analyse sont disponibles',
                    date: new Date().toLocaleDateString('fr-FR'),
                    icon: 'fas fa-stethoscope',
                    color: 'text-green-600'
                },
                {
                    id: 3,
                    type: 'prescription',
                    message: 'Nouvelle prescription disponible',
                    date: new Date().toLocaleDateString('fr-FR'),
                    icon: 'fas fa-pills',
                    color: 'text-purple-600'
                }
            ];

            displayNotifications(notifications);
        }

        function displayNotifications(notifications) {
            const container = document.getElementById('notifications-list');
            const noNotifications = document.getElementById('no-notifications');

            noNotifications.classList.add('hidden');
            container.innerHTML = '';

            if (notifications.length === 0) {
                noNotifications.classList.remove('hidden');
                return;
            }

            notifications.forEach(notification => {
                const div = document.createElement('div');
                div.className = 'flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors';
                
                div.innerHTML = `
                    <div class="flex-shrink-0 mr-4">
                        <i class="${notification.icon} ${notification.color} text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">${notification.message}</p>
                        <p class="text-xs text-gray-500 mt-1">${notification.date}</p>
                    </div>
                    <button onclick="markAsRead(${notification.id})" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                
                container.appendChild(div);
            });
        }

        function markAsRead(notificationId) {
            // Simulation de marquage comme lu
            console.log('Notification marquée comme lue:', notificationId);
            loadNotifications(); // Recharger les notifications
        }

        // Initialiser
        document.addEventListener('DOMContentLoaded', function() {
            loadPatientData();
            loadNotifications();
        });
    </script>
</body>
</html> 