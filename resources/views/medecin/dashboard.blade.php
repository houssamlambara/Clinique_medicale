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
                
                <button onclick="loadPrescriptions()" class="bg-orange-500 text-white p-4 rounded text-center">
                    <i class="fas fa-pills text-2xl mb-2"></i>
                    <div class="font-semibold">Mes Prescriptions</div>
                </button>
            </div>
        </div>
    </main>

    <script>
        var currentMedecin = null;

        // Charger les données du médecin
        function loadMedecinData() {
            var userData = localStorage.getItem('user_data');
            if (userData) {
                currentMedecin = JSON.parse(userData);
                document.getElementById('userName').textContent = 'Dr. ' + currentMedecin.nom + ' ' + currentMedecin.prenom;
                loadRendezVous();
            } else {
                alert('Aucun utilisateur connecté');
            }
        }

        // Charger les rendez-vous du médecin
        function loadRendezVous() {
            if (!currentMedecin) return;

            fetch('http://127.0.0.1:8000/api/rendezvous/medecin/' + currentMedecin.id)
                .then(function(response) {
                    return response.json();
                })
                .then(function(data) {
                    var container = document.getElementById('rendezvous-list');
                    if (data.success && data.data.length > 0) {
                        var html = '';
                        data.data.forEach(function(rdv) {
                            var date = new Date(rdv.date_rdv).toLocaleDateString('fr-FR');
                            html += '<div class="border-b py-2">';
                            html += '<div class="font-semibold">' + date + '</div>';
                            if (rdv.patient) {
                                html += '<div class="text-sm text-gray-600">Patient: ' + rdv.patient.nom + ' ' + rdv.patient.prenom + '</div>';
                            }
                            html += '</div>';
                        });
                        container.innerHTML = html;
                    } else {
                        container.innerHTML = '<p class="text-gray-500">Aucun rendez-vous</p>';
                    }
                })
                .catch(function(error) {
                    console.error('Erreur:', error);
                    document.getElementById('rendezvous-list').innerHTML = '<p class="text-red-500">Erreur de chargement</p>';
                });
        }

        // Charger les consultations du médecin
        function loadConsultations() {
            if (!currentMedecin) return;

            fetch('http://127.0.0.1:8000/api/consultations/medecin/' + currentMedecin.id)
                .then(function(response) {
                    return response.json();
                })
                .then(function(data) {
                    var container = document.getElementById('consultations-list');
                    if (data.success && data.data.length > 0) {
                        var html = '';
                        data.data.forEach(function(consultation) {
                            var date = new Date(consultation.date_consultation).toLocaleDateString('fr-FR');
                            html += '<div class="border-b py-2">';
                            html += '<div class="font-semibold">' + date + '</div>';
                            html += '<div class="text-sm text-gray-600">' + (consultation.statut || 'Consultation') + '</div>';
                            if (consultation.patient) {
                                html += '<div class="text-sm text-gray-600">Patient: ' + consultation.patient.nom + ' ' + consultation.patient.prenom + '</div>';
                            }
                            html += '</div>';
                        });
                        container.innerHTML = html;
                    } else {
                        container.innerHTML = '<p class="text-gray-500">Aucune consultation</p>';
                    }
                })
                .catch(function(error) {
                    console.error('Erreur:', error);
                    document.getElementById('consultations-list').innerHTML = '<p class="text-red-500">Erreur de chargement</p>';
                });
        }



        // Charger les prescriptions du médecin
        function loadPrescriptions() {
            if (!currentMedecin) return;

            fetch('http://127.0.0.1:8000/api/prescriptions')
                .then(function(response) {
                    return response.json();
                })
                .then(function(data) {
                    if (data.success && data.data.length > 0) {
                        var prescriptions = data.data.filter(function(prescription) {
                            return prescription.medecin_id === currentMedecin.id;
                        });
                        
                        if (prescriptions.length > 0) {
                            var html = '';
                            prescriptions.forEach(function(prescription) {
                                var date = new Date(prescription.date_prescription).toLocaleDateString('fr-FR');
                                html += '<div class="border-b py-2">';
                                html += '<div class="font-semibold">' + date + '</div>';
                                html += '<div class="text-sm text-gray-600">' + (prescription.medicaments || 'Prescription') + '</div>';
                                if (prescription.patient) {
                                    html += '<div class="text-sm text-gray-600">Patient: ' + prescription.patient.nom + ' ' + prescription.patient.prenom + '</div>';
                                }
                                html += '</div>';
                            });
                            alert('Prescriptions:\n' + html.replace(/<[^>]*>/g, '\n'));
                        } else {
                            alert('Aucune prescription trouvée');
                        }
                    } else {
                        alert('Aucune prescription trouvée');
                    }
                })
                .catch(function(error) {
                    console.error('Erreur:', error);
                    alert('Erreur de chargement des prescriptions');
                });
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