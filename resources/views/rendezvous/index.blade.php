<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Liste des Rendez-vous</title>
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
                        <i class="fas fa-calendar-check text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Mes Rendez-vous</h1>
                        <p class="text-sm text-gray-600">Vos rendez-vous médicaux</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="/patient/dashboard" class="bg-gradient-to-r from-gray-500 to-gray-600 text-white px-6 py-2 rounded-lg hover:from-gray-600 hover:to-gray-700 transition-all duration-200 transform hover:scale-105 shadow-md">
                        <i class="fas fa-arrow-left mr-2"></i>Retour
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <!-- Liste des rendez-vous -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900">Mes Rendez-vous</h2>
                <div class="flex items-center space-x-2">
                    <button onclick="loadRendezVous()" class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-4 py-2 rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 transform hover:scale-105 shadow-md text-sm">
                        <i class="fas fa-sync-alt mr-2"></i>Actualiser
                    </button>
                </div>
            </div>

            <!-- Loading -->
            <div id="loading" class="text-center py-12">
                <i class="fas fa-spinner fa-spin text-blue-500 text-4xl mb-4"></i>
                <p class="text-gray-600">Chargement des rendez-vous...</p>
            </div>

            <!-- Liste -->
            <div id="rendezvous-list" class="space-y-4">
                <!-- Les rendez-vous seront chargés ici -->
            </div>

            <!-- Message si aucun rendez-vous -->
            <div id="no-rendezvous" class="hidden text-center py-12">
                <i class="fas fa-calendar-times text-gray-400 text-4xl mb-4"></i>
                <p class="text-gray-500 font-medium">Aucun rendez-vous trouvé</p>
                <p class="text-sm text-gray-400 mt-2">Vous n'avez pas encore de rendez-vous</p>
            </div>

            <!-- Message d'erreur -->
            <div id="error-message" class="hidden text-center py-12">
                <i class="fas fa-exclamation-triangle text-red-400 text-4xl mb-4"></i>
                <p class="text-red-500 font-medium" id="error-text">Erreur de chargement</p>
            </div>
        </div>
    </main>

    <!-- Script -->
    <script>
        // Charger les rendez-vous
        function loadRendezVous() {
            // Afficher le loading
            document.getElementById('loading').classList.remove('hidden');
            document.getElementById('rendezvous-list').innerHTML = '';
            document.getElementById('no-rendezvous').classList.add('hidden');
            document.getElementById('error-message').classList.add('hidden');
            const userData = localStorage.getItem('user_data');
            const user = JSON.parse(userData);
            const patientId = user.patient.id;
            console.log(patientId);

            // Appel API direct (routes publiques)
            fetch(`http://127.0.0.1:8000/api/rendezvous/patient/${patientId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur de connexion au serveur');
                    }
                    return response.json();
                })
                .then(data => {
                    document.getElementById('loading').classList.add('hidden');
                    
                    if (data.success && data.data.length > 0) {
                        console.log(data.data);
                        displayRendezVous(data.data);
                    } else {
                        document.getElementById('no-rendezvous').classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    document.getElementById('loading').classList.add('hidden');
                    showError(error.message);
                });
        }

        // Afficher les rendez-vous
        function displayRendezVous(rendezvous) {
            var container = document.getElementById('rendezvous-list');
            container.innerHTML = '';

            rendezvous.forEach(function(rdv) {
                var div = createRendezVousElement(rdv);
                container.appendChild(div);
            });
        }

        // Créer un élément de rendez-vous
        function createRendezVousElement(rdv) {
            var div = document.createElement('div');
            div.className = 'bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 border border-gray-100 p-6';
            
            var date = new Date(rdv.date_rdv).toLocaleDateString('fr-FR');
            var time = new Date(rdv.date_rdv).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
            var isToday = new Date(rdv.date_rdv).toDateString() === new Date().toDateString();
            var isPast = new Date(rdv.date_rdv) < new Date();
            
            var statusClass = isPast ? 'bg-red-100 text-red-800' : 
                            isToday ? 'bg-green-100 text-green-800' : 
                            'bg-blue-100 text-blue-800';
            
            var statusText = isPast ? 'Passé' : 
                           isToday ? 'Aujourd\'hui' : 
                           'À venir';

            div.innerHTML = `
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center mb-3">
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-2 rounded-lg mr-3">
                                <i class="fas fa-calendar-check text-white text-sm"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Rendez-vous #${rdv.id}</h4>
                                <p class="text-sm text-gray-600">${date}</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            ${rdv.patient ? `
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <p class="text-xs font-medium text-gray-600">Patient</p>
                                    <p class="text-sm font-semibold text-gray-900">${rdv.patient.user.nom} ${rdv.patient.user.prenom}</p>
                                </div>
                            ` : ''}
                            
                            ${rdv.medecin ? `
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <p class="text-xs font-medium text-gray-600">Médecin</p>
                                    <p class="text-sm font-semibold text-gray-900">Dr. ${rdv.medecin.user.nom} ${rdv.medecin.user.prenom}</p>
                                </div>
                            ` : ''}
                        </div>
                        
                        ${rdv.raison ? `
                            <div class="text-sm text-gray-600">
                                <i class="fas fa-info-circle mr-2"></i>
                                <span>${rdv.raison}</span>
                            </div>
                        ` : ''}
                    </div>
                </div>
            `;

            return div;
        }

        // Afficher une erreur
        function showError(message) {
            document.getElementById('error-text').textContent = message;
            document.getElementById('error-message').classList.remove('hidden');
        }

        // Initialiser la page
        document.addEventListener('DOMContentLoaded', function() {
            loadRendezVous();
        });
    </script>
</body>
</html> 