let currentPatient = null;
let dossiersCache = null;

document.addEventListener('DOMContentLoaded', function () {
    loadUserData();
});

function loadUserData() {
    console.log('Chargement des données utilisateur...');
    const userData = localStorage.getItem('user_data');
    console.log('User data:', userData);
    
    if (userData) {
        currentPatient = JSON.parse(userData);
        console.log('Patient connecté:', currentPatient);
        
        if (currentPatient.role !== 'patient') {
            console.log('Rôle incorrect:', currentPatient.role);
            showError('Accès non autorisé');
            return;
        }
        loadDossiers();
    } else {
        console.log('Aucun utilisateur connecté');
        showError('Aucun utilisateur connecté');
    }
}

function loadDossiers() {
    console.log('Chargement des dossiers...');
    
    if (!currentPatient) {
        console.log('Aucun patient connecté');
        showError('Aucun patient connecté');
        return;
    }

    const token = localStorage.getItem('auth_token');
    if (!token) {
        console.log('Token manquant');
        showError('Token d\'authentification manquant');
        return;
    }

    console.log('URL de l\'API:', `http://127.0.0.1:8000/api/dossiers/patient/${currentPatient.id}`);
    console.log('Token:', token);

    fetch(`http://127.0.0.1:8000/api/dossiers/patient/${currentPatient.id}`, {
        method: 'GET',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        console.log('Réponse API:', response.status, response.statusText);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Données reçues:', data);
        if (data.success) {
            dossiersCache = data.data;
            console.log('Dossiers trouvés:', data.data.length);
            displayDossiers(data.data);
        } else {
            console.log('Erreur API:', data.message);
            showError(data.message || 'Erreur lors du chargement des dossiers');
        }
    })
    .catch(error => {
        console.log('Erreur fetch:', error);
        showError('Erreur de connexion au serveur: ' + error.message);
    });
}

function displayDossiers(dossiers) {
    const container = document.getElementById('dossiers-list');
    const noDossiers = document.getElementById('no-dossiers');
    const errorMessage = document.getElementById('error-message');

    noDossiers.classList.add('hidden');
    errorMessage.classList.add('hidden');

    if (!dossiers || dossiers.length === 0) {
        container.innerHTML = '';
        noDossiers.classList.remove('hidden');
        return;
    }

    let html = '';
    dossiers.forEach(dossier => {
        const dateCreation = new Date(dossier.created_at).toLocaleDateString('fr-FR');
        const dateModification = new Date(dossier.updated_at).toLocaleDateString('fr-FR');
        const prescriptionsCount = dossier.prescriptions ? dossier.prescriptions.length : 0;

        html += `
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-3 rounded-xl">
                            <i class="fas fa-folder text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Dossier #${dossier.id}</h3>
                            <p class="text-sm text-gray-600">Créé le ${dateCreation}</p>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <!-- Informations du dossier -->
                    <div class="space-y-4">
                        <h4 class="text-md font-semibold text-gray-900 mb-3">
                            <i class="fas fa-info-circle mr-2 text-blue-500"></i>Informations du dossier
                        </h4>
                        
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">ID Dossier:</span>
                                    <span class="text-sm font-medium text-gray-900">${dossier.id}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Date de création:</span>
                                    <span class="text-sm font-medium text-gray-900">${dateCreation}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Dernière modification:</span>
                                    <span class="text-sm font-medium text-gray-900">${dateModification}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Prescriptions:</span>
                                    <span class="text-sm font-medium text-gray-900">${prescriptionsCount}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes du dossier -->
                <div class="space-y-4">
                    <h4 class="text-md font-semibold text-gray-900 mb-3">
                        <i class="fas fa-notes-medical mr-2 text-purple-500"></i>Notes médicales
                    </h4>
                    
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">${dossier.note || 'Aucune note disponible'}</p>
                    </div>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
}

function showSuccess(message) {
    const successMessage = document.getElementById('successMessage');
    successMessage.querySelector('span').textContent = message;
    successMessage.classList.remove('hidden');

    setTimeout(() => {
        successMessage.classList.add('hidden');
    }, 3000);
}

function showError(message) {
    const errorMessage = document.getElementById('error-message');
    const errorText = document.getElementById('error-text');
    const noDossiers = document.getElementById('no-dossiers');
    const dossiersList = document.getElementById('dossiers-list');

    errorText.textContent = message;
    errorMessage.classList.remove('hidden');
    noDossiers.classList.add('hidden');
    dossiersList.innerHTML = '';
} 