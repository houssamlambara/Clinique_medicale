// Variables globales
let currentMedecin = null;
let rendezvousCache = null;

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    loadUserData();
});

// Charger les données utilisateur
function loadUserData() {
    const userData = localStorage.getItem('user_data');
    if (userData) {
        currentMedecin = JSON.parse(userData);
        if (currentMedecin.role !== 'medecin') {
            showError('Accès non autorisé');
            return;
        }
        loadRendezVous();
    } else {
        showError('Aucun utilisateur connecté');
    }
}

// Charger les rendez-vous du médecin
function loadRendezVous() {
    if (!currentMedecin) {
        console.error('Aucun médecin connecté');
        showError('Aucun médecin connecté');
        return;
    }

    const token = localStorage.getItem('auth_token');
    if (!token) {
        console.error('Token d\'authentification manquant');
        showError('Token d\'authentification manquant');
        return;
    }

    console.log('Chargement des rendez-vous pour le médecin:', currentMedecin.id);
    console.log('Token disponible:', token ? 'Oui' : 'Non');

    fetch(`http://127.0.0.1:8000/api/rendezvous/medecin/${currentMedecin.id}`, {
        method: 'GET',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        console.log('Réponse du serveur:', response.status, response.statusText);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Données reçues:', data);
        if (data.success) {
            rendezvousCache = data.data;
            displayRendezVous(data.data);
        } else {
            showError(data.message || 'Erreur lors du chargement des rendez-vous');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showError('Erreur de connexion au serveur: ' + error.message);
    });
}

// Afficher les rendez-vous
function displayRendezVous(rendezvous) {
    const container = document.getElementById('rendezvous-list');
    const noRendezVous = document.getElementById('no-rendezvous');
    const errorMessage = document.getElementById('error-message');

    // Masquer les messages
    noRendezVous.classList.add('hidden');
    errorMessage.classList.add('hidden');

    if (!rendezvous || rendezvous.length === 0) {
        container.innerHTML = '';
        noRendezVous.classList.remove('hidden');
        return;
    }

    let html = '';
    rendezvous.forEach(rdv => {
        const date = new Date(rdv.date_rdv).toLocaleDateString('fr-FR');
        const time = rdv.heure_rdv ? rdv.heure_rdv.substring(0, 5) : '--:--';

        html += `
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-3 rounded-xl">
                            <i class="fas fa-calendar-check text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Rendez-vous #${rdv.id}</h3>
                            <div class="flex items-center gap-4">
                                <div class="flex items-center gap-1">
                                    <i class="fas fa-calendar text-blue-500 text-sm"></i>
                                    <p class="text-sm text-gray-600">${date}</p>
                                </div>
                                <div class="flex items-center gap-1">
                                    <i class="fas fa-clock text-green-500 text-sm"></i>
                                    <p class="text-sm text-gray-600">${time}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-2">
                            <i class="fas fa-user mr-2 text-blue-500"></i>Patient
                        </h4>
                        <p class="text-gray-700">${rdv.patient ? rdv.patient.user.nom + ' ' + rdv.patient.user.prenom : 'Patient non spécifié'}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-2">
                            <i class="fas fa-user-md mr-2 text-green-500"></i>Médecin
                        </h4>
                        <p class="text-gray-700">Dr. ${rdv.medecin ? rdv.medecin.user.nom + ' ' + rdv.medecin.user.prenom : currentMedecin.nom + ' ' + currentMedecin.prenom}</p>
                    </div>
                </div>

                ${rdv.motif ? `
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <h4 class="font-medium text-gray-900 mb-2">
                            <i class="fas fa-notes-medical mr-2 text-purple-500"></i>Motif
                        </h4>
                        <p class="text-gray-700">${rdv.motif}</p>
                    </div>
                ` : ''}
            </div>
        `;
    });

    container.innerHTML = html;
}
// Afficher un message de succès
function showSuccess(message) {
    const successMessage = document.getElementById('successMessage');
    successMessage.querySelector('span').textContent = message;
    successMessage.classList.remove('hidden');
    
    setTimeout(() => {
        successMessage.classList.add('hidden');
    }, 3000);
}

// Afficher un message d'erreur
function showError(message) {
    const errorMessage = document.getElementById('error-message');
    const errorText = document.getElementById('error-text');
    const noRendezVous = document.getElementById('no-rendezvous');
    const rendezvousList = document.getElementById('rendezvous-list');

    errorText.textContent = message;
    errorMessage.classList.remove('hidden');
    noRendezVous.classList.add('hidden');
    rendezvousList.innerHTML = '';
}