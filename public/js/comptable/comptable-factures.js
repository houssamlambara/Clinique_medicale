let currentComptable = null;
let factures = [];
let patients = [];
let consultations = [];

document.addEventListener('DOMContentLoaded', function () {
    loadUserData();
    loadFactures();
    loadPatients();
});

function loadUserData() {
    const userData = localStorage.getItem('user_data');
    
    if (userData) {
        currentComptable = JSON.parse(userData);
        
        if (currentComptable.role !== 'comptable') {
            showError('Accès non autorisé');
            return;
        }
        
        // Vérifier si l'élément existe avant de le modifier
        const comptableNameElement = document.getElementById('comptable-name');
        if (comptableNameElement) {
            comptableNameElement.textContent = `${currentComptable.prenom} ${currentComptable.nom}`;
        }
    } else {
        showError('Aucun utilisateur connecté');
    }
}

function loadFactures() {
    const token = localStorage.getItem('auth_token');
    if (!token) {
        showError('Token d\'authentification manquant');
        return;
    }

    fetch('http://127.0.0.1:8000/api/factures', {
        method: 'GET',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
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

function loadPatients() {
    const token = localStorage.getItem('auth_token');
    if (!token) return;

    fetch('http://127.0.0.1:8000/api/patients', {
        method: 'GET',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            patients = data.data;
            populatePatientSelects();
        }
    })
    .catch(error => {
        console.log('Erreur chargement patients:', error);
    });
}

function loadConsultations(patientId = null) {
    const token = localStorage.getItem('auth_token');
    if (!token) return;

    let url = 'http://127.0.0.1:8000/api/consultations';
    if (patientId) {
        url += `?patient_id=${patientId}`;
    }

    fetch(url, {
        method: 'GET',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            consultations = data.data;
            populateConsultationSelect();
        }
    })
    .catch(error => {
        console.log('Erreur chargement consultations:', error);
    });
}

function populatePatientSelects() {
    const patientSelect = document.getElementById('patient-id');
    const filterPatientSelect = document.getElementById('filter-patient');
    
    // Vider les options existantes
    patientSelect.innerHTML = '<option value="">Sélectionner un patient</option>';
    filterPatientSelect.innerHTML = '<option value="">Tous les patients</option>';
    
    patients.forEach(patient => {
        const option = document.createElement('option');
        option.value = patient.id;
        option.textContent = `${patient.user.prenom} ${patient.user.nom}`;
        patientSelect.appendChild(option);
        
        const filterOption = document.createElement('option');
        filterOption.value = patient.id;
        filterOption.textContent = `${patient.user.prenom} ${patient.user.nom}`;
        filterPatientSelect.appendChild(filterOption);
    });
}

function populateConsultationSelect() {
    const consultationSelect = document.getElementById('consultation-id');
    consultationSelect.innerHTML = '<option value="">Sélectionner une consultation</option>';
    
    consultations.forEach(consultation => {
        const option = document.createElement('option');
        option.value = consultation.id;
        
        // Formater la date correctement
        const date = consultation.date_consultation ? new Date(consultation.date_consultation).toLocaleDateString('fr-FR') : 'Date non définie';
        const patientName = consultation.patient?.user ? `${consultation.patient.user.prenom} ${consultation.patient.user.nom}` : 'Patient non défini';
        
        option.textContent = `Consultation #${consultation.id} - ${patientName}`;
        consultationSelect.appendChild(option);
    });
}

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
        const div = document.createElement('div');
        div.className = 'bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow';
        
        const statusClass = facture.est_paye ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
        const statusText = facture.est_paye ? 'Payée' : 'Non payée';
        
        div.innerHTML = `
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h4 class="text-lg font-semibold text-gray-900">Facture #${facture.id}</h4>
                    <p class="text-sm text-gray-600">Patient: ${facture.consultation?.patient?.user?.prenom} ${facture.consultation?.patient?.user?.nom}</p>
                    <p class="text-sm text-gray-600">Consultation: #${facture.consultation_id}</p>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="px-3 py-1 rounded-full text-xs font-medium ${statusClass}">${statusText}</span>
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
                    <p class="text-lg font-bold text-gray-900">${parseFloat(facture.montant).toLocaleString()} €</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-700">Date de création</p>
                    <p class="text-sm text-gray-600">${new Date(facture.created_at).toLocaleDateString('fr-FR')}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-700">Date de paiement</p>
                    <p class="text-sm text-gray-600">${facture.date_paiement ? new Date(facture.date_paiement).toLocaleDateString('fr-FR') : 'Non payée'}</p>
                </div>
            </div>
            ${facture.description ? `
            <div class="mt-4">
                <p class="text-sm font-medium text-gray-700">Description</p>
                <p class="text-sm text-gray-600">${facture.description}</p>
            </div>
            ` : ''}
        `;
        
        container.appendChild(div);
    });
}

function filterFactures() {
    const statusFilter = document.getElementById('filter-status').value;
    const patientFilter = document.getElementById('filter-patient').value;
    const dateStartFilter = document.getElementById('filter-date-start').value;
    const dateEndFilter = document.getElementById('filter-date-end').value;

    let filteredFactures = factures;

    if (statusFilter) {
        filteredFactures = filteredFactures.filter(f => {
            if (statusFilter === 'paye') return f.est_paye;
            if (statusFilter === 'non-paye') return !f.est_paye;
            return true;
        });
    }

    if (patientFilter) {
        filteredFactures = filteredFactures.filter(f => f.patient_id == patientFilter);
    }

    if (dateStartFilter) {
        filteredFactures = filteredFactures.filter(f => f.created_at >= dateStartFilter);
    }

    if (dateEndFilter) {
        filteredFactures = filteredFactures.filter(f => f.created_at <= dateEndFilter);
    }

    displayFactures(filteredFactures);
}

function showCreateForm() {
    document.getElementById('modal-title').textContent = 'Nouvelle Facture';
    document.getElementById('facture-form').reset();
    document.getElementById('facture-id').value = '';
    document.getElementById('facture-modal').classList.remove('hidden');
    loadConsultations();
}

function editFacture(id) {
    const facture = factures.find(f => f.id === id);
    if (!facture) return;

    document.getElementById('modal-title').textContent = 'Modifier la Facture';
    document.getElementById('facture-id').value = facture.id;
    document.getElementById('consultation-id').value = facture.consultation_id;
    document.getElementById('montant').value = facture.montant;
    
    // Charger les consultations pour ce patient
    loadConsultations(facture.consultation?.patient?.id);
    
    document.getElementById('facture-modal').classList.remove('hidden');
}

function hideModal() {
    document.getElementById('facture-modal').classList.add('hidden');
}

function deleteFacture(id) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cette facture ?')) return;

    const token = localStorage.getItem('auth_token');
    if (!token) {
        showError('Token d\'authentification manquant');
        return;
    }

    fetch(`http://127.0.0.1:8000/api/factures/${id}`, {
        method: 'DELETE',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
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
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
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

document.getElementById('patient-id').addEventListener('change', function() {
    const patientId = this.value;
    if (patientId) {
        loadConsultations(patientId);
    } else {
        populateConsultationSelect();
    }
});

function logout() {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user_data');
    window.location.href = '/login';
}

function showSuccess(message) {
    alert('Succès: ' + message);
}

function showError(message) {
    alert('Erreur: ' + message);
} 