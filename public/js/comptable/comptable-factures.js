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
    
    if (!userData) {
        return;
    }
    
    currentComptable = JSON.parse(userData);
    
    if (currentComptable.role !== 'comptable') {
        return;
    }
    
    // Afficher le nom du comptable
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

    // Appel API pour récupérer les factures
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

// Charger tous les patients depuis l'API
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
        // Erreur silencieuse pour les patients
    });
}

// Charger les consultations d'un patient (ou toutes si aucun patient spécifié)
function loadConsultations(patientId = null) {
    const token = localStorage.getItem('auth_token');
    if (!token) {
        console.error('Token manquant pour charger les consultations');
        return;
    }

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
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            consultations = data.data;
            populateConsultationSelect();
        } else {
            showError('Erreur lors du chargement des consultations: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erreur lors du chargement des consultations:', error);
        showError('Erreur de connexion lors du chargement des consultations');
    });
}

// Remplir les listes déroulantes des patients
function populatePatientSelects() {
    const patientSelect = document.getElementById('patient-id');
    const filterPatientSelect = document.getElementById('filter-patient');
    
    // Vider les listes
    patientSelect.innerHTML = '<option value="">Sélectionner un patient</option>';
    filterPatientSelect.innerHTML = '<option value="">Tous les patients</option>';
    
    // Ajouter chaque patient
    patients.forEach(patient => {
        const patientName = `${patient.user.prenom} ${patient.user.nom}`;
        
        // Option pour le formulaire
        const option = document.createElement('option');
        option.value = patient.id;
        option.textContent = patientName;
        patientSelect.appendChild(option);
        
        // Option pour le filtre
        const filterOption = document.createElement('option');
        filterOption.value = patient.id;
        filterOption.textContent = patientName;
        filterPatientSelect.appendChild(filterOption);
    });
}

// Remplir la liste déroulante des consultations
function populateConsultationSelect() {
    const consultationSelect = document.getElementById('consultation-id');
    if (!consultationSelect) {
        console.error('Élément consultation-id non trouvé');
        return;
    }
    
    consultationSelect.innerHTML = '<option value="">Sélectionner une consultation</option>';
    
    // Ajouter chaque consultation
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

// Afficher la liste des factures
function displayFactures(facturesToDisplay) {
    const container = document.getElementById('factures-list');
    const noFactures = document.getElementById('no-factures');

    // Vider le conteneur
    container.innerHTML = '';

    // Si aucune facture, afficher le message
    if (facturesToDisplay.length === 0) {
        noFactures.classList.remove('hidden');
        return;
    }

    // Cacher le message "aucune facture"
    noFactures.classList.add('hidden');

    // Créer une carte pour chaque facture
    facturesToDisplay.forEach(facture => {
        const factureCard = createFactureCard(facture);
        container.appendChild(factureCard);
    });
}

// Créer une carte HTML pour une facture
function createFactureCard(facture) {
    const div = document.createElement('div');
    div.className = 'bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow';
    
    // Déterminer le statut de paiement
    const isPayee = facture.est_paye;
    const statusClass = isPayee ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
    const statusText = isPayee ? 'Payée' : 'Non payée';
    
    // Récupérer les informations du patient
    const patientName = facture.consultation?.patient?.user ? 
        `${facture.consultation.patient.user.prenom} ${facture.consultation.patient.user.nom}` : 
        'Patient non défini';
    
    // Formater les données
    const montant = parseFloat(facture.montant).toLocaleString();
    const dateCreation = new Date(facture.created_at).toLocaleDateString('fr-FR');
    const datePaiement = facture.date_paiement ? 
        new Date(facture.date_paiement).toLocaleDateString('fr-FR') : 
        'Non payée';
    
    div.innerHTML = `
        <div class="flex justify-between items-start mb-4">
            <div>
                <h4 class="text-lg font-semibold text-gray-900">Facture #${facture.id}</h4>
                <p class="text-sm text-gray-600">Patient: ${patientName}</p>
                <p class="text-sm text-gray-600">Consultation: #${facture.consultation_id}</p>
            </div>
            <div class="flex items-center space-x-2">
                <select onchange="changerStatutFacture(${facture.id}, this.value)" class="px-4 py-2 rounded-lg text-sm border-2 border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white shadow-sm hover:border-gray-300 ${isPayee ? 'border-green-300 bg-green-50' : 'border-red-300 bg-red-50'}">
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

// Filtrer les factures selon les critères
function filterFactures() {
    // Récupérer les valeurs des filtres
    const statusFilter = document.getElementById('filter-status').value;
    const patientFilter = document.getElementById('filter-patient').value;
    const dateStartFilter = document.getElementById('filter-date-start').value;
    const dateEndFilter = document.getElementById('filter-date-end').value;

    let filteredFactures = factures;

    // Filtrer par statut
    if (statusFilter) {
        filteredFactures = filteredFactures.filter(facture => {
            if (statusFilter === 'paye') return facture.est_paye;
            if (statusFilter === 'non-paye') return !facture.est_paye;
            return true;
        });
    }

    // Filtrer par patient
    if (patientFilter) {
        filteredFactures = filteredFactures.filter(facture => 
            facture.consultation?.patient_id == patientFilter
        );
    }

    // Filtrer par date de début
    if (dateStartFilter) {
        filteredFactures = filteredFactures.filter(facture => 
            facture.created_at >= dateStartFilter
        );
    }

    // Filtrer par date de fin
    if (dateEndFilter) {
        filteredFactures = filteredFactures.filter(facture => 
            facture.created_at <= dateEndFilter
        );
    }

    // Afficher les résultats filtrés
    displayFactures(filteredFactures);
}

// Afficher le formulaire de création
function showCreateForm() {
    document.getElementById('modal-title').textContent = 'Nouvelle Facture';
    document.getElementById('facture-form').reset();
    document.getElementById('facture-id').value = '';
    document.getElementById('facture-modal').classList.remove('hidden');
    
    // Utiliser les consultations déjà chargées
    populateConsultationSelect();
}

// Afficher le formulaire d'édition
function editFacture(id) {
    const facture = factures.find(f => f.id === id);
    if (!facture) return;

    // Remplir le formulaire avec les données de la facture
    document.getElementById('modal-title').textContent = 'Modifier la Facture';
    document.getElementById('facture-id').value = facture.id;
    document.getElementById('consultation-id').value = facture.consultation_id;
    document.getElementById('montant').value = facture.montant;
    
    // Charger les consultations du patient
    loadConsultations(facture.consultation?.patient?.id);
    
    document.getElementById('facture-modal').classList.remove('hidden');
}

// Cacher le modal
function hideModal() {
    document.getElementById('facture-modal').classList.add('hidden');
}

// Marquer une facture comme payée
function marquerCommePayee(id) {
    if (!confirm('Marquer cette facture comme payée ?')) return;
    
    const token = localStorage.getItem('auth_token');
    if (!token) {
        showError('Token d\'authentification manquant');
        return;
    }

    const formData = {
        est_paye: true,
        date_paiement: new Date().toISOString().split('T')[0]
    };

    // Appel API pour mettre à jour le statut
    fetch(`http://127.0.0.1:8000/api/factures/${id}`, {
        method: 'PUT',
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
            loadFactures();
            showSuccess('Facture marquée comme payée');
        } else {
            showError(data.message || 'Erreur lors de la mise à jour');
        }
    })
    .catch(error => {
        showError('Erreur de connexion au serveur');
    });
}

// Marquer une facture comme non payée
function marquerCommeNonPayee(id) {
    if (!confirm('Marquer cette facture comme non payée ?')) return;
    
    const token = localStorage.getItem('auth_token');
    if (!token) {
        showError('Token d\'authentification manquant');
        return;
    }

    const formData = {
        est_paye: false,
        date_paiement: null
    };

    // Appel API pour mettre à jour le statut
    fetch(`http://127.0.0.1:8000/api/factures/${id}`, {
        method: 'PUT',
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
            loadFactures();
            showSuccess('Facture marquée comme non payée');
        } else {
            showError(data.message || 'Erreur lors de la mise à jour');
        }
    })
    .catch(error => {
        showError('Erreur de connexion au serveur');
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

    // Appel API pour mettre à jour le statut
    fetch(`http://127.0.0.1:8000/api/factures/${id}`, {
        method: 'PUT',
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

    // Appel API pour supprimer
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

// Gestion de la soumission du formulaire
document.getElementById('facture-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Récupérer les données du formulaire
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

    // Déterminer l'URL et la méthode selon si c'est une création ou modification
    const url = isEdit ? `http://127.0.0.1:8000/api/factures/${factureId}` : 'http://127.0.0.1:8000/api/factures';
    const method = isEdit ? 'PUT' : 'POST';

    // Appel API pour créer ou modifier
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

// Charger les consultations quand un patient est sélectionné
document.getElementById('patient-id').addEventListener('change', function() {
    const patientId = this.value;
    if (patientId) {
        loadConsultations(patientId);
    } else {
        populateConsultationSelect();
    }
});

// Déconnexion
function logout() {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user_data');
    window.location.href = '/login';
}

// Afficher un message de succès
function showSuccess(message) {
    alert('Succès: ' + message);
}

// Afficher un message d'erreur
function showError(message) {
    alert('Erreur: ' + message);
} 