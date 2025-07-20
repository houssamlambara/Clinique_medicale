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

    fetch(`/api/rendezvous/medecin/${currentMedecin.id}`, {
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
        const time = new Date(rdv.date_rdv).toLocaleTimeString('fr-FR', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
        
        const statusClass = getStatusClass(rdv.statut);
        const statusText = getStatusText(rdv.statut);

        html += `
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-3 rounded-xl">
                            <i class="fas fa-calendar-check text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Rendez-vous #${rdv.id}</h3>
                            <p class="text-sm text-gray-600">${date} à ${time}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="px-3 py-1 rounded-full text-xs font-medium ${statusClass}">
                            ${statusText}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-2">
                            <i class="fas fa-user mr-2 text-blue-500"></i>Patient
                        </h4>
                        <p class="text-gray-700">${rdv.patient ? rdv.patient.nom + ' ' + rdv.patient.prenom : 'Patient non spécifié'}</p>
                        ${rdv.patient ? `<p class="text-sm text-gray-500">ID: ${rdv.patient.id}</p>` : ''}
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-2">
                            <i class="fas fa-user-md mr-2 text-green-500"></i>Médecin
                        </h4>
                        <p class="text-gray-700">Dr. ${rdv.medecin ? rdv.medecin.nom + ' ' + rdv.medecin.prenom : currentMedecin.nom + ' ' + currentMedecin.prenom}</p>
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

                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    <div class="text-sm text-gray-500">
                        <i class="fas fa-clock mr-1"></i>
                        Créé le ${new Date(rdv.created_at).toLocaleDateString('fr-FR')}
                    </div>
                    <div class="flex items-center space-x-2">
                        <button onclick="updateRendezVousStatus(${rdv.id}, 'confirmé')" 
                                class="px-3 py-1 bg-green-500 text-white text-xs rounded hover:bg-green-600 transition-colors">
                            <i class="fas fa-check mr-1"></i>Confirmer
                        </button>
                        <button onclick="updateRendezVousStatus(${rdv.id}, 'annulé')" 
                                class="px-3 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600 transition-colors">
                            <i class="fas fa-times mr-1"></i>Annuler
                        </button>
                    </div>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
}

// Mettre à jour le statut d'un rendez-vous
function updateRendezVousStatus(rendezVousId, newStatus) {
    if (!currentMedecin) return;

    const token = localStorage.getItem('auth_token');
    if (!token) {
        showError('Token d\'authentification manquant');
        return;
    }

    fetch(`/api/rendezvous/${rendezVousId}`, {
        method: 'PUT',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            statut: newStatus
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showSuccess('Statut du rendez-vous mis à jour avec succès');
            loadRendezVous(); // Recharger les données
        } else {
            showError(data.message || 'Erreur lors de la mise à jour');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showError('Erreur de connexion au serveur');
    });
}

// Obtenir la classe CSS pour le statut
function getStatusClass(status) {
    switch (status) {
        case 'confirmé':
            return 'bg-green-100 text-green-800';
        case 'en_attente':
            return 'bg-yellow-100 text-yellow-800';
        case 'annulé':
            return 'bg-red-100 text-red-800';
        case 'terminé':
            return 'bg-blue-100 text-blue-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}

// Obtenir le texte du statut
function getStatusText(status) {
    switch (status) {
        case 'confirmé':
            return 'Confirmé';
        case 'en_attente':
            return 'En attente';
        case 'annulé':
            return 'Annulé';
        case 'terminé':
            return 'Terminé';
        default:
            return 'Inconnu';
    }
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