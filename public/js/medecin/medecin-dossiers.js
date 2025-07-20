let currentMedecin = null;
let dossiersCache = null;

document.addEventListener('DOMContentLoaded', function () {
    loadUserData();
});

function loadUserData() {
    const userData = localStorage.getItem('user_data');
    if (userData) {
        currentMedecin = JSON.parse(userData);
        if (currentMedecin.role !== 'medecin') {
            showError('Accès non autorisé');
            return;
        }
        loadDossiers();
    } else {
        showError('Aucun utilisateur connecté');
    }
}

function loadDossiers() {
    if (!currentMedecin) {
        showError('Aucun médecin connecté');
        return;
    }

    const token = localStorage.getItem('auth_token');
    if (!token) {
        showError('Token d\'authentification manquant');
        return;
    }

    fetch(`http://127.0.0.1:8000/api/dossiers/medecin/${currentMedecin.id}`, {
        method: 'GET',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                dossiersCache = data.data;
                displayDossiers(data.data);
            } else {
                showError(data.message || 'Erreur lors du chargement des dossiers');
            }
        })
        .catch(error => {
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
                        <div class="bg-gradient-to-r from-teal-500 to-cyan-600 p-3 rounded-xl">
                            <i class="fas fa-folder-medical text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Dossier #${dossier.id}</h3>
                            <p class="text-sm text-gray-600">Patient: ${dossier.patient.user.nom} ${dossier.patient.user.prenom}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button onclick="editDossier(${dossier.id})" 
                                class="bg-teal-500 hover:bg-teal-600 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center gap-2">
                            <i class="fas fa-edit"></i>
                            Modifier
                        </button>
                        <button onclick="deleteDossier(${dossier.id})" 
                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center gap-2">
                            <i class="fas fa-trash"></i>
                            Supprimer
                        </button>
                    </div>
                </div>

                <div class="mb-6">
                    <!-- Informations du patient -->
                    <div class="space-y-4">
                        <h4 class="text-md font-semibold text-gray-900 mb-3">
                            <i class="fas fa-user mr-2 text-blue-500"></i>Informations du patient
                        </h4>
                        
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Nom:</span>
                                    <span class="text-sm font-medium text-gray-900">${dossier.patient.user.nom}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Prénom:</span>
                                    <span class="text-sm font-medium text-gray-900">${dossier.patient.user.prenom}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Email:</span>
                                    <span class="text-sm font-medium text-gray-900">${dossier.patient.user.email || 'Non spécifié'}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Téléphone:</span>
                                    <span class="text-sm font-medium text-gray-900">${dossier.patient.user.telephone || 'Non spécifié'}</span>
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

function deleteDossier(dossierId) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer ce dossier médical ? Cette action est irréversible.')) {
        return;
    }

    const token = localStorage.getItem('auth_token');
    if (!token) {
        showError('Token d\'authentification manquant');
        return;
    }

    fetch(`http://127.0.0.1:8000/api/dossiers/${dossierId}`, {
        method: 'DELETE',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showSuccess('Dossier supprimé avec succès');
                loadDossiers();
            } else {
                showError(data.message || 'Erreur lors de la suppression');
            }
        })
        .catch(error => {
            showError('Erreur de connexion au serveur: ' + error.message);
        });
}

function editDossier(dossierId) {
    const dossier = dossiersCache.find(d => d.id === dossierId);
    if (!dossier) {
        showError('Dossier non trouvé');
        return;
    }

    document.getElementById('editDossierId').value = dossierId;
    document.getElementById('editNoteInput').value = dossier.note || '';
    document.getElementById('editDossierModal').classList.remove('hidden');
}

function closeEditDossierModal() {
    document.getElementById('editDossierModal').classList.add('hidden');
}

function updateDossier() {
    const dossierId = document.getElementById('editDossierId').value;
    const note = document.getElementById('editNoteInput').value;

    if (!dossierId) {
        showError('ID du dossier manquant');
        return;
    }

    const token = localStorage.getItem('auth_token');
    if (!token) {
        showError('Token d\'authentification manquant');
        return;
    }

    fetch(`http://127.0.0.1:8000/api/dossiers/${dossierId}`, {
        method: 'PUT',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            note: note
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
                showSuccess('Dossier mis à jour avec succès');
                closeEditDossierModal();
                loadDossiers();
            } else {
                showError(data.message || 'Erreur lors de la mise à jour');
            }
        })
        .catch(error => {
            showError('Erreur de connexion au serveur: ' + error.message);
        });
}

function viewPatientConsultations(patientId) {
    showSuccess('Fonctionnalité des consultations à implémenter');
}

function viewPatientPrescriptions(patientId) {
    showSuccess('Fonctionnalité des prescriptions à implémenter');
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

// Fonctions pour créer un dossier
function openCreateDossierModal() {
    loadPatientsForCreate();
    document.getElementById('createDossierModal').classList.remove('hidden');
}

function closeCreateDossierModal() {
    document.getElementById('createDossierModal').classList.add('hidden');
    document.getElementById('createDossierForm').reset();
}

function loadPatientsForCreate() {
    if (!currentMedecin) {
        showError('Aucun médecin connecté');
        return;
    }

    const token = localStorage.getItem('auth_token');
    if (!token) {
        showError('Token d\'authentification manquant');
        return;
    }

    fetch(`http://127.0.0.1:8000/api/patients/medecin/${currentMedecin.id}`, {
        method: 'GET',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                populatePatientSelect(data.data);
            } else {
                showError(data.message || 'Erreur lors du chargement des patients');
            }
        })
        .catch(error => {
            showError('Erreur de connexion au serveur: ' + error.message);
        });
}

function populatePatientSelect(patients) {
    const select = document.getElementById('createPatientSelect');
    select.innerHTML = '<option value="">Sélectionnez un patient...</option>';

    patients.forEach(patient => {
        const option = document.createElement('option');
        option.value = patient.id;
        option.textContent = `${patient.user.nom} ${patient.user.prenom}`;
        select.appendChild(option);
    });
}

function createDossier() {
    const patientId = document.getElementById('createPatientSelect').value;
    const note = document.getElementById('createNoteInput').value;

    if (!patientId) {
        showError('Veuillez sélectionner un patient');
        return;
    }

    const token = localStorage.getItem('auth_token');
    if (!token) {
        showError('Token d\'authentification manquant');
        return;
    }

    fetch('http://127.0.0.1:8000/api/dossiers', {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            patient_id: patientId,
            note: note
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
                showSuccess('Dossier créé avec succès');
                closeCreateDossierModal();
                loadDossiers();
            } else {
                showError(data.message || 'Erreur lors de la création du dossier');
            }
        })
        .catch(error => {
            showError('Erreur de connexion au serveur: ' + error.message);
        });
} 