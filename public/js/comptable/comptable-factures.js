// Variables globales
let currentComptable = null;
let factures = [];
let patients = [];
let consultations = [];

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function () {
    loadUserData();
    loadFactures();
    loadPatients();
    loadConsultations(); 
});

// Charger les données de l'utilisateur connecté
function loadUserData() {
    const userData = localStorage.getItem('user_data');
    if (!userData) return;
    
    currentComptable = JSON.parse(userData);
    if (currentComptable.role !== 'comptable') return;
    
    const comptableNameElement = document.getElementById('comptable-name');
    if (comptableNameElement) {
        comptableNameElement.textContent = `${currentComptable.prenom} ${currentComptable.nom}`;
    }
}

// Charger toutes les factures depuis l'API
function loadFactures() {
    const token = localStorage.getItem('auth_token');
    if (!token) {
        showError('Token d\'authentification manquant');
        return;
    }

    fetch('http://127.0.0.1:8000/api/factures', {
        method: 'GET',
        headers: getHeaders(token)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            factures = data.data;
            displayFactures(factures);
        } else {
            showError(data.message || 'Erreur lors du chargement des factures');
        }
    })
    .catch(error => {
        showError('Erreur de connexion au serveur');
    });
}

// Charger tous les patients depuis l'API
function loadPatients() {
    const token = localStorage.getItem('auth_token');
    if (!token) return;

    fetch('http://127.0.0.1:8000/api/patients', {
        method: 'GET',
        headers: getHeaders(token)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            patients = data.data;
            populatePatientSelects();
        }
    })
    .catch(error => {
    });
}

// Charger les consultations d'un patient (ou toutes si aucun patient spécifié)
function loadConsultations(patientId = null) {
    const token = localStorage.getItem('auth_token');
    if (!token) return;

    let url = 'http://127.0.0.1:8000/api/consultations';
    if (patientId) {
        url += `?patient_id=${patientId}`;
    }

    fetch(url, {
        method: 'GET',
        headers: getHeaders(token)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            consultations = data.data;
            populateConsultationSelect();
        } else {
            showError('Erreur lors du chargement des consultations: ' + data.message);
        }
    })
    .catch(error => {
        showError('Erreur de connexion lors du chargement des consultations');
    });
}

// Afficher la liste des factures
function displayFactures(facturesToDisplay) {
    const container = document.getElementById('factures-list');
    const noFactures = document.getElementById('no-factures');

    container.innerHTML = '';

    if (facturesToDisplay.length === 0) {
        noFactures.classList.remove('hidden');
        return;
    }

    noFactures.classList.add('hidden');

    facturesToDisplay.forEach(facture => {
        const factureCard = createFactureCard(facture);
        container.appendChild(factureCard);
    });
}

// Créer une carte HTML pour une facture
function createFactureCard(facture) {
    const div = document.createElement('div');
    div.className = 'bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow';
    
    const patientName = facture.consultation?.patient?.user ? 
        `${facture.consultation.patient.user.prenom} ${facture.consultation.patient.user.nom}` : 
        'Patient non défini';
    
    const montant = parseFloat(facture.montant).toLocaleString();
    const dateCreation = new Date(facture.created_at).toLocaleDateString('fr-FR');
    const datePaiement = facture.date_paiement ? 
        new Date(facture.date_paiement).toLocaleDateString('fr-FR') : 
        'Non payée';
    
    const isPayee = facture.est_paye;
    const statusClass = isPayee ? 'border-green-300 bg-green-50' : 'border-red-300 bg-red-50';
    
    div.innerHTML = `
        <div class="flex justify-between items-start mb-4">
            <div>
                <h4 class="text-lg font-semibold text-gray-900">Facture #${facture.id}</h4>
                <p class="text-sm text-gray-600">Patient: ${patientName}</p>
                <p class="text-sm text-gray-600">Consultation: #${facture.consultation_id}</p>
            </div>
            <div class="flex items-center space-x-2">
                <select onchange="changerStatutFacture(${facture.id}, this.value)" class="px-4 py-2 rounded-lg text-sm border-2 border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white shadow-sm hover:border-gray-300 ${statusClass}">
                    <option value="false" ${!isPayee ? 'selected' : ''} class="text-red-700">Non payée</option>
                    <option value="true" ${isPayee ? 'selected' : ''} class="text-green-700">Payée</option>
                </select>
                <div class="flex space-x-2">
                    <button onclick="editFacture(${facture.id})" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition-colors">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteFacture(${facture.id})" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition-colors">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-sm font-medium text-gray-700">Montant</p>
                <p class="text-lg font-bold text-gray-900">${montant} €</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-700">Date de création</p>
                <p class="text-sm text-gray-600">${dateCreation}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-700">Date de paiement</p>
                <p class="text-sm text-gray-600">${datePaiement}</p>
            </div>
        </div>
    `;
    
    return div;
}

// Remplir les listes déroulantes des patients
function populatePatientSelects() {
    const patientSelect = document.getElementById('patient-id');
    const filterPatientSelect = document.getElementById('filter-patient');
    
    patientSelect.innerHTML = '<option value="">Sélectionner un patient</option>';
    filterPatientSelect.innerHTML = '<option value="">Tous les patients</option>';
    
    patients.forEach(patient => {
        const patientName = `${patient.user.prenom} ${patient.user.nom}`;
        
        const option = document.createElement('option');
        option.value = patient.id;
        option.textContent = patientName;
        patientSelect.appendChild(option);
        
        const filterOption = document.createElement('option');
        filterOption.value = patient.id;
        filterOption.textContent = patientName;
        filterPatientSelect.appendChild(filterOption);
    });
}

// Remplir la liste déroulante des consultations
function populateConsultationSelect() {
    const consultationSelect = document.getElementById('consultation-id');
    if (!consultationSelect) return;
    
    consultationSelect.innerHTML = '<option value="">Sélectionner une consultation</option>';
    
    consultations.forEach(consultation => {
        const patientName = consultation.patient?.user ? 
            `${consultation.patient.user.prenom} ${consultation.patient.user.nom}` : 
            'Patient non défini';
        
        const option = document.createElement('option');
        option.value = consultation.id;
        option.textContent = `Consultation #${consultation.id} - ${patientName}`;
        consultationSelect.appendChild(option);
    });
}

// Changer le statut d'une facture via le select
function changerStatutFacture(id, newStatus) {
    const token = localStorage.getItem('auth_token');
    if (!token) {
        showError('Token d\'authentification manquant');
        return;
    }

    const isPayee = newStatus === 'true';
    const formData = {
        est_paye: isPayee,
        date_paiement: isPayee ? new Date().toISOString().split('T')[0] : null
    };

    fetch(`http://127.0.0.1:8000/api/factures/${id}`, {
        method: 'PUT',
        headers: getHeaders(token),
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadFactures();
            showSuccess(isPayee ? 'Facture marquée comme payée' : 'Facture marquée comme non payée');
        } else {
            showError(data.message || 'Erreur lors de la mise à jour');
        }
    })
    .catch(error => {
        showError('Erreur de connexion au serveur');
    });
}

// Supprimer une facture
function deleteFacture(id) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cette facture ?')) return;

    const token = localStorage.getItem('auth_token');
    if (!token) {
        showError('Token d\'authentification manquant');
        return;
    }

    fetch(`http://127.0.0.1:8000/api/factures/${id}`, {
        method: 'DELETE',
        headers: getHeaders(token)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadFactures();
            showSuccess('Facture supprimée avec succès');
        } else {
            showError(data.message || 'Erreur lors de la suppression');
        }
    })
    .catch(error => {
        showError('Erreur de connexion au serveur');
    });
}

// Afficher le formulaire de création
function showCreateForm() {
    document.getElementById('modal-title').textContent = 'Nouvelle Facture';
    document.getElementById('facture-form').reset();
    document.getElementById('facture-id').value = '';
    document.getElementById('facture-modal').classList.remove('hidden');
    populateConsultationSelect();
}

// Afficher le formulaire d'édition
function editFacture(id) {
    const facture = factures.find(f => f.id === id);
    if (!facture) return;

    document.getElementById('modal-title').textContent = 'Modifier la Facture';
    document.getElementById('facture-id').value = facture.id;
    document.getElementById('consultation-id').value = facture.consultation_id;
    document.getElementById('montant').value = facture.montant;
    
    loadConsultations(facture.consultation?.patient?.id);
    document.getElementById('facture-modal').classList.remove('hidden');
}

// Cacher le modal
function hideModal() {
    document.getElementById('facture-modal').classList.add('hidden');
}

// Filtrer les factures selon les critères
function filterFactures() {
    const statusFilter = document.getElementById('filter-status').value;
    const patientFilter = document.getElementById('filter-patient').value;

    let filteredFactures = factures;

    if (statusFilter) {
        filteredFactures = filteredFactures.filter(facture => {
            if (statusFilter === 'paye') return facture.est_paye;
            if (statusFilter === 'non-paye') return !facture.est_paye;
            return true;
        });
    }

    if (patientFilter) {
        filteredFactures = filteredFactures.filter(facture => 
            facture.consultation?.patient_id == patientFilter
        );
    }

    displayFactures(filteredFactures);
}

// Obtenir les headers pour les requêtes API
function getHeaders(token) {
    return {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    };
}

// Afficher un message de succès
function showSuccess(message) {
    alert('Succès: ' + message);
}

// Afficher un message d'erreur
function showError(message) {
    alert('Erreur: ' + message);
}

// Déconnexion
function logout() {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user_data');
    window.location.href = '/login';
}

// Gestion de la soumission du formulaire
document.getElementById('facture-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = {
        consultation_id: document.getElementById('consultation-id').value,
        date_facture: new Date().toISOString().split('T')[0],
        montant: document.getElementById('montant').value
    };

    const factureId = document.getElementById('facture-id').value;
    const isEdit = factureId !== '';

    const token = localStorage.getItem('auth_token');
    if (!token) {
        showError('Token d\'authentification manquant');
        return;
    }

    const url = isEdit ? `http://127.0.0.1:8000/api/factures/${factureId}` : 'http://127.0.0.1:8000/api/factures';
    const method = isEdit ? 'PUT' : 'POST';

    fetch(url, {
        method: method,
        headers: getHeaders(token),
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            hideModal();
            loadFactures();
            showSuccess(isEdit ? 'Facture modifiée avec succès' : 'Facture créée avec succès');
        } else {
            showError(data.message || 'Erreur lors de l\'enregistrement');
        }
    })
    .catch(error => {
        showError('Erreur de connexion au serveur');
    });
});

// Charger les consultations quand un patient est sélectionné
document.getElementById('patient-id').addEventListener('change', function() {
    const patientId = this.value;
    if (patientId) {
        loadConsultations(patientId);
    } else {
        populateConsultationSelect();
    }
}); 