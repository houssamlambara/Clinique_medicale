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
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
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

                <a href="/patient/dossiers" class="bg-gradient-to-r from-teal-500 to-cyan-600 text-white p-6 rounded-xl text-center hover:from-teal-600 hover:to-cyan-700 transition-all duration-200 transform hover:scale-105 shadow-lg">
                    <i class="fas fa-folder text-3xl mb-3"></i>
                    <div class="font-semibold text-lg">Mes Dossiers</div>
                    <p class="text-sm opacity-90 mt-1">Consulter mes dossiers médicaux</p>
                </a>

                <a href="/patient/prescriptions" class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white p-6 rounded-xl text-center hover:from-indigo-600 hover:to-purple-700 transition-all duration-200 transform hover:scale-105 shadow-lg">
                    <i class="fas fa-pills text-3xl mb-3"></i>
                    <div class="font-semibold text-lg">Mes Prescriptions</div>
                    <p class="text-sm opacity-90 mt-1">Consulter mes prescriptions</p>
                </a>
            </div>
        </div>
    </main>

    <script>
        // Charger les données du patient
        function loadPatientData() {
            var userData = localStorage.getItem('user_data');
            if (userData) {
                var currentPatient = JSON.parse(userData);
                document.getElementById('userName').textContent = currentPatient.nom + ' ' + currentPatient.prenom;
            }
        }

        function logout() {
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user_data');
            window.location.href = '/login';
        }

        // Initialiser
        document.addEventListener('DOMContentLoaded', function() {
            loadPatientData();
        });
    </script>
</body>

</html>