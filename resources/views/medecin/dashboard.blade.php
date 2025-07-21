<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Médecin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-user-md text-green-500 text-2xl"></i>
                    <h1 class="text-xl font-bold">Dashboard Médecin</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span id="userName">Chargement...</span>
                    <button onclick="logout()" class="bg-red-500 text-white px-4 py-2 rounded">
                        <i class="fas fa-sign-out-alt mr-2"></i>Déconnexion
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 px-4">
        <!-- Actions Rapides -->
        <div class="mb-8">
            <h2 class="text-lg font-bold mb-4">Actions Rapides</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <button onclick="window.location.href='/medecin/rendezvous'" class="bg-blue-500 text-white p-4 rounded text-center">
                    <i class="fas fa-calendar-check text-2xl mb-2"></i>
                    <div class="font-semibold">Mes Rendez-vous</div>
                </button>
                
                <button onclick="window.location.href='/medecin/consultations'" class="bg-green-500 text-white p-4 rounded text-center">
                    <i class="fas fa-stethoscope text-2xl mb-2"></i>
                    <div class="font-semibold">Mes Consultations</div>
                </button>
                
                <button onclick="window.location.href='/medecin/patients'" class="bg-purple-500 text-white p-4 rounded text-center">
                    <i class="fas fa-users text-2xl mb-2"></i>
                    <div class="font-semibold">Mes Patients</div>
                </button>
                
                <button onclick="window.location.href='/medecin/dossiers'" class="bg-teal-500 text-white p-4 rounded text-center">
                    <i class="fas fa-folder text-2xl mb-2"></i>
                    <div class="font-semibold">Mes Dossiers</div>
                </button>
                
                <button onclick="window.location.href='/medecin/prescriptions'" class="bg-orange-500 text-white p-4 rounded text-center">
                    <i class="fas fa-pills text-2xl mb-2"></i>
                    <div class="font-semibold">Mes Prescriptions</div>
                </button>
            </div>
        </div>
    </main>

    <script>
        // Charger les données du médecin
        function loadMedecinData() {
            var userData = localStorage.getItem('user_data');
            if (userData) {
                var currentMedecin = JSON.parse(userData);
                document.getElementById('userName').textContent = 'Dr. ' + currentMedecin.nom + ' ' + currentMedecin.prenom;
            }
        }

        function logout() {
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user_data');
            window.location.href = '/login';
        }

        // Initialiser
        document.addEventListener('DOMContentLoaded', function() {
            loadMedecinData();
        });
    </script>
</body>
</html> 
</html> 