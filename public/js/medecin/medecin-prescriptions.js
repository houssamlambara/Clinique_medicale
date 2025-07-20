let currentMedecin = null;
let prescriptionsCache = null;
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
        loadPrescriptions();
    } else {
        showError('Aucun utilisateur connecté');
    }
}

function loadPrescriptions() {
    if (!currentMedecin) {
        showError('Aucun médecin connecté');
        return;
    }

    const token = localStorage.getItem('auth_token');
    if (!token) {
        showError('Token d\'authentification manquant');
        return;
    }

    fetch(`http://127.0.0.1:8000/api/prescriptions/medecin/${currentMedecin.id}`, {
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
            prescriptionsCache = data.data;
            displayPrescriptions(data.data);
        } else {
            showError(data.message || 'Erreur lors du chargement des prescriptions');
        }
    })
    .catch(error => {
        showError('Erreur de connexion au serveur: ' + error.message);
    });
}

function displayPrescriptions(prescriptions) {
    const container = document.getElementById('prescriptions-list');
    const noPrescriptions = document.getElementById('no-prescriptions');
    const errorMessage = document.getElementById('error-message');

    noPrescriptions.classList.add('hidden');
    errorMessage.classList.add('hidden');

    if (!prescriptions || prescriptions.length === 0) {
        container.innerHTML = '';
        noPrescriptions.classList.remove('hidden');
        return;
    }

    let html = '';
    prescriptions.forEach(prescription => {
        const dateCreation = new Date(prescription.created_at).toLocaleDateString('fr-FR');
        const patientName = prescription.dossier_medical?.patient?.user ? 
            `${prescription.dossier_medical.patient.user.nom} ${prescription.dossier_medical.patient.user.prenom}` : 
            'Patient inconnu';
        const medecinName = prescription.medecin?.user ? 
            `Dr. ${prescription.medecin.user.nom} ${prescription.medecin.user.prenom}` : 
            'Médecin inconnu';

        html += `
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="bg-gradient-to-r from-orange-500 to-red-600 p-3 rounded-xl">
                            <i class="fas fa-pills text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Prescription #${prescription.id}</h3>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button onclick="editPrescription(${prescription.id})" 
                                class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center gap-2">
                            <i class="fas fa-edit"></i>
                            Modifier
                        </button>
                        <button onclick="deletePrescription(${prescription.id})" 
                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center gap-2">
                            <i class="fas fa-trash"></i>
                            Supprimer
                        </button>
                    </div>
                </div>

                <div class="mb-6">
                    <!-- Informations de la prescription -->
                    <div class="space-y-4">
                        <h4 class="text-md font-semibold text-gray-900 mb-3">
                            <i class="fas fa-info-circle mr-2 text-orange-500"></i>Informations de la prescription
                        </h4>
                        
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Patient:</span>
                                    <span class="text-sm font-medium text-gray-900">${patientName}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Médecin:</span>
                                    <span class="text-sm font-medium text-gray-900">${medecinName}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Date de création:</span>
                                    <span class="text-sm font-medium text-gray-900">${dateCreation}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Dossier médical:</span>
                                    <span class="text-sm font-medium text-gray-900">#${prescription.dossier_medical_id}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Médicament -->
                <div class="space-y-4">
                    <h4 class="text-md font-semibold text-gray-900 mb-3">
                        <i class="fas fa-pills mr-2 text-red-500"></i>Médicament
                    </h4>
                    
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">${prescription.medicament || 'Aucun médicament spécifié'}</p>
                    </div>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
}

// Fonctions pour créer une prescription
function openCreatePrescriptionModal() {
    loadDossiersForCreate();
    document.getElementById('createPrescriptionModal').classList.remove('hidden');
}

function closeCreatePrescriptionModal() {
    document.getElementById('createPrescriptionModal').classList.add('hidden');
    document.getElementById('createPrescriptionForm').reset();
}

function loadDossiersForCreate() {
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
            populateDossierSelect(data.data);
        } else {
            showError(data.message || 'Erreur lors du chargement des dossiers');
        }
    })
    .catch(error => {
        showError('Erreur de connexion au serveur: ' + error.message);
    });
}

function populateDossierSelect(dossiers) {
    const select = document.getElementById('createDossierSelect');
    select.innerHTML = '<option value="">Sélectionnez un dossier médical...</option>';
    
    dossiers.forEach(dossier => {
        const option = document.createElement('option');
        option.value = dossier.id;
        const patientName = dossier.patient?.user ? 
            `${dossier.patient.user.nom} ${dossier.patient.user.prenom}` : 
            'Patient inconnu';
        option.textContent = `Dossier #${dossier.id} - ${patientName}`;
        select.appendChild(option);
    });
}

function createPrescription() {
    const dossierId = document.getElementById('createDossierSelect').value;
    const medicament = document.getElementById('createMedicamentInput').value;

    if (!dossierId) {
        showError('Veuillez sélectionner un dossier médical');
        return;
    }

    if (!medicament.trim()) {
        showError('Veuillez spécifier le médicament');
        return;
    }

    const token = localStorage.getItem('auth_token');
    if (!token) {
        showError('Token d\'authentification manquant');
        return;
    }

    fetch('http://127.0.0.1:8000/api/prescriptions', {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            dossier_medical_id: dossierId,
            medecin_id: currentMedecin.id,
            medicament: medicament
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
            showSuccess('Prescription créée avec succès');
            closeCreatePrescriptionModal();
            loadPrescriptions();
        } else {
            showError(data.message || 'Erreur lors de la création de la prescription');
        }
    })
    .catch(error => {
        showError('Erreur de connexion au serveur: ' + error.message);
    });
}

// Fonctions pour modifier une prescription
function editPrescription(prescriptionId) {
    const prescription = prescriptionsCache.find(p => p.id === prescriptionId);
    if (!prescription) {
        showError('Prescription non trouvée');
        return;
    }

    document.getElementById('editPrescriptionId').value = prescriptionId;
    document.getElementById('editMedicamentInput').value = prescription.medicament || '';
    document.getElementById('editPrescriptionModal').classList.remove('hidden');
}

function closeEditPrescriptionModal() {
    document.getElementById('editPrescriptionModal').classList.add('hidden');
}

function updatePrescription() {
    const prescriptionId = document.getElementById('editPrescriptionId').value;
    const medicament = document.getElementById('editMedicamentInput').value;

    if (!medicament.trim()) {
        showError('Veuillez spécifier le médicament');
        return;
    }

    const token = localStorage.getItem('auth_token');
    if (!token) {
        showError('Token d\'authentification manquant');
        return;
    }

    fetch(`http://127.0.0.1:8000/api/prescriptions/${prescriptionId}`, {
        method: 'PUT',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            medicament: medicament
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
            showSuccess('Prescription mise à jour avec succès');
            closeEditPrescriptionModal();
            loadPrescriptions();
        } else {
            showError(data.message || 'Erreur lors de la mise à jour');
        }
    })
    .catch(error => {
        showError('Erreur de connexion au serveur: ' + error.message);
    });
}

// Fonction pour supprimer une prescription
function deletePrescription(prescriptionId) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cette prescription ? Cette action est irréversible.')) {
        return;
    }

    const token = localStorage.getItem('auth_token');
    if (!token) {
        showError('Token d\'authentification manquant');
        return;
    }

    fetch(`http://127.0.0.1:8000/api/prescriptions/${prescriptionId}`, {
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
            showSuccess('Prescription supprimée avec succès');
            loadPrescriptions();
        } else {
            showError(data.message || 'Erreur lors de la suppression');
        }
    })
    .catch(error => {
        showError('Erreur de connexion au serveur: ' + error.message);
    });
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
    const noPrescriptions = document.getElementById('no-prescriptions');
    const prescriptionsList = document.getElementById('prescriptions-list');

    errorText.textContent = message;
    errorMessage.classList.remove('hidden');
    noPrescriptions.classList.add('hidden');
    prescriptionsList.innerHTML = '';
} 