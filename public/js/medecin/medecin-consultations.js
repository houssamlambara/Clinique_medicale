let usersCache = {};
let patients = [];

function getAuthHeaders() {
    const token = localStorage.getItem('auth_token');
    return {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': token ? `Bearer ${token}` : '',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
    };
}

function getUserData(userId) {
    if (usersCache[userId]) return Promise.resolve(usersCache[userId]);
    
    return fetch(`http://127.0.0.1:8000/api/users/${userId}`, {
        headers: getAuthHeaders()
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            usersCache[userId] = data.data;
            return data.data;
        }
        return null;
    })
    .catch(error => {
        console.error('Erreur:', error);
        return null;
    });
}

function loadConsultations() {
    const userData = localStorage.getItem('user_data');
    const token = localStorage.getItem('auth_token');
    
    if (!userData) {
        showError('Aucun utilisateur connecté');
        return;
    }
    
    if (!token) {
        showError('Token d\'authentification manquant');
        return;
    }

    const medecinId = JSON.parse(userData).medecin.id;
    
    fetch(`http://127.0.0.1:8000/api/consultations/medecin/${medecinId}`, {
        headers: getAuthHeaders()
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Consultations data:', data);
        if (data.success) {
            displayConsultations(data.data);
        } else {
            showError(data.message || 'Erreur lors du chargement des consultations');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showError('Erreur lors du chargement des consultations: ' + error.message);
    });
}

function displayConsultations(consultations) {
    const container = document.getElementById('consultations-list');
    const noConsultations = document.getElementById('no-consultations');
    const errorMessage = document.getElementById('error-message');

    errorMessage.classList.add('hidden');
    noConsultations.classList.add('hidden');
    container.innerHTML = '';

    if (consultations.length === 0) {
        noConsultations.classList.remove('hidden');
        return;
    }

    consultations.forEach(consultation => {
        container.appendChild(createConsultationElement(consultation));
        loadUserDataForConsultation(consultation);
    });
}

function createConsultationElement(consultation) {
    const div = document.createElement('div');
    div.className = 'bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 border border-gray-100 p-6';

    const date = new Date(consultation.created_at).toLocaleDateString('fr-FR');
    const statutClass = getStatutClass(consultation.statut);
    const statutBgClass = getStatutBgClass(consultation.statut);

    div.innerHTML = `
        <div class="flex-1">
            <div class="flex items-center mb-4">
                <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-3 rounded-lg mr-4">
                    <i class="fas fa-stethoscope text-white text-lg"></i>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <h4 class="font-bold text-gray-900 text-lg">Consultation #${consultation.id}</h4>
                        <span class="px-3 py-1 rounded-full text-xs font-medium ${statutBgClass} ${statutClass}">
                            <i class="fas fa-circle mr-1"></i>${consultation.statut}
                        </span>
                    </div>
                    <div class="flex items-center gap-3 mt-1">
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-calendar mr-1"></i>${date}
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                ${consultation.patient ? `
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-user text-blue-500 mr-2"></i>
                            <p class="text-xs font-medium text-gray-600">Patient</p>
                        </div>
                        <p class="text-sm font-semibold text-gray-900" id="patient-${consultation.id}">Chargement...</p>
                    </div>
                ` : '<div class="bg-gray-50 p-4 rounded-lg border border-gray-200"><p class="text-sm text-gray-500">Patient: Données non disponibles</p></div>'}
                
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-euro-sign text-green-500 mr-2"></i>
                        <p class="text-xs font-medium text-gray-600">Montant</p>
                    </div>
                    <p class="text-sm font-semibold text-gray-900">${consultation.montant} €</p>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-user-md text-green-500 mr-2"></i>
                        <p class="text-xs font-medium text-gray-600">Actions</p>
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="editConsultation(${consultation.id})" class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs">
                            <i class="fas fa-edit mr-1"></i>Modifier
                        </button>
                        <button onclick="deleteConsultation(${consultation.id})" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs">
                            <i class="fas fa-trash mr-1"></i>Supprimer
                        </button>
                    </div>
                </div>
            </div>
            
            ${consultation.motif ? `
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-notes-medical text-blue-500 mr-2"></i>
                        <p class="text-xs font-medium text-gray-600">Motif de la consultation</p>
                    </div>
                    <p class="text-sm text-gray-700">${consultation.motif}</p>
                </div>
            ` : ''}
        </div>
    `;

    return div;
}

function loadUserDataForConsultation(consultation) {
    if (consultation.patient) {
        getUserData(consultation.patient.user_id)
            .then(patientUser => {
                const patientElement = document.getElementById(`patient-${consultation.id}`);
                if (patientElement) {
                    patientElement.textContent = patientUser ? `${patientUser.nom} ${patientUser.prenom}` : 'Non disponible';
                }
            });
    }
}

function loadPatients() {
    const userData = localStorage.getItem('user_data');
    if (!userData) {
        console.error('Aucun utilisateur connecté');
        return;
    }

    const medecinId = JSON.parse(userData).medecin.id;
    console.log('Chargement des patients pour le médecin:', medecinId);
    
    fetch(`http://127.0.0.1:8000/api/patients/medecin/${medecinId}`, {
        headers: getAuthHeaders()
    })
    .then(response => {
        console.log('Réponse API patients:', response.status, response.statusText);
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Données patients reçues:', data);
        if (data.success) {
            patients = data.data;
            console.log('Patients chargés:', patients.length);
            populatePatientSelect();
        } else {
            console.error('Erreur API patients:', data.message);
            showError(data.message || 'Erreur lors du chargement des patients');
        }
    })
    .catch(error => {
        console.error('Erreur lors du chargement des patients:', error);
        showError('Erreur de connexion au serveur: ' + error.message);
    });
}

function populatePatientSelect() {
    const select = document.getElementById('patientSelect');
    console.log('PopulatePatientSelect - Select element:', select);
    
    if (!select) {
        console.error('Element patientSelect non trouvé');
        return;
    }
    
    select.innerHTML = '<option value="">Sélectionnez un patient</option>';
    console.log('Patients à ajouter:', patients.length);
    
    patients.forEach(patient => {
        const option = document.createElement('option');
        option.value = patient.id;
        option.textContent = `${patient.user.nom} ${patient.user.prenom}`;
        select.appendChild(option);
        console.log('Patient ajouté:', patient.user.nom, patient.user.prenom);
    });
    
    console.log('Select rempli avec', patients.length, 'patients');
}

function loadPatientsForEdit(selectedPatientId) {
    const userData = localStorage.getItem('user_data');
    if (!userData) return;

    const medecinId = JSON.parse(userData).medecin.id;
    
    fetch(`http://127.0.0.1:8000/api/patients/medecin/${medecinId}`, {
        headers: getAuthHeaders()
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const select = document.getElementById('editPatientSelect');
            select.innerHTML = '<option value="">Sélectionnez un patient</option>';
            
            data.data.forEach(patient => {
                const option = document.createElement('option');
                option.value = patient.id;
                option.textContent = `${patient.user.nom} ${patient.user.prenom}`;
                if (patient.id == selectedPatientId) {
                    option.selected = true;
                }
                select.appendChild(option);
            });
        }
    })
    .catch(error => {
        console.error('Erreur lors du chargement des patients:', error);
    });
}

function openCreateConsultationModal() {
    document.getElementById('createConsultationModal').classList.remove('hidden');
    loadPatients();
}

function closeCreateConsultationModal() {
    document.getElementById('createConsultationModal').classList.add('hidden');
    document.getElementById('createConsultationForm').reset();
}

function editConsultation(consultationId) {
    fetch(`http://127.0.0.1:8000/api/consultations/${consultationId}`, {
        headers: getAuthHeaders()
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const consultation = data.data;
            populateEditModal(consultation);
            document.getElementById('editConsultationModal').classList.remove('hidden');
        } else {
            showError('Erreur lors du chargement de la consultation');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showError('Erreur lors du chargement de la consultation');
    });
}

function populateEditModal(consultation) {
    document.getElementById('editConsultationId').value = consultation.id;
    document.getElementById('editMotifInput').value = consultation.motif || '';
    document.getElementById('editMontantInput').value = consultation.montant || '';
    document.getElementById('editStatutInput').value = consultation.statut || 'en_cours';
    
    loadPatientsForEdit(consultation.patient_id);
}

function closeEditConsultationModal() {
    document.getElementById('editConsultationModal').classList.add('hidden');
    document.getElementById('editConsultationForm').reset();
}

function deleteConsultation(consultationId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette consultation ?')) {
        fetch(`http://127.0.0.1:8000/api/consultations/${consultationId}`, {
            method: 'DELETE',
            headers: getAuthHeaders()
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess('Consultation supprimée avec succès');
                loadConsultations();
            } else {
                showError('Erreur lors de la suppression');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showError('Erreur lors de la suppression');
        });
    }
}

function getStatutClass(statut) {
    switch(statut) {
        case 'en_cours':
            return 'text-yellow-600';
        case 'terminée':
            return 'text-green-600';
        case 'annulée':
            return 'text-red-600';
        default:
            return 'text-gray-600';
    }
}

function getStatutBgClass(statut) {
    switch(statut) {
        case 'en_cours':
            return 'bg-yellow-100';
        case 'terminée':
            return 'bg-green-100';
        case 'annulée':
            return 'bg-red-100';
        default:
            return 'bg-gray-100';
    }
}

function showError(message) {
    document.getElementById('error-text').textContent = message;
    document.getElementById('error-message').classList.remove('hidden');
}

function showSuccess(message) {
    const successMessage = document.getElementById('successMessage');
    successMessage.querySelector('span').textContent = message;
    successMessage.classList.remove('hidden');
    
    setTimeout(() => {
        successMessage.classList.add('hidden');
    }, 3000);
}

document.addEventListener('DOMContentLoaded', function () {
    // Vérifier l'authentification
    const token = localStorage.getItem('auth_token');
    const userData = localStorage.getItem('user_data');
    
    if (!token || !userData) {
        alert('Veuillez vous connecter pour accéder à cette page');
        window.location.href = '/login';
        return;
    }
    
    // Vérifier que c'est bien un médecin
    try {
        const user = JSON.parse(userData);
        if (user.role !== 'medecin' || !user.medecin) {
            alert('Accès réservé aux médecins');
            window.location.href = '/login';
            return;
        }
    } catch (error) {
        console.error('Erreur parsing user data:', error);
        alert('Erreur de données utilisateur');
        window.location.href = '/login';
        return;
    }
    
    loadConsultations();
    
    document.getElementById('createConsultationForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            patient_id: document.getElementById('patientSelect').value,
            motif: document.getElementById('motifInput').value,
            montant: document.getElementById('montantInput').value,
            statut: document.getElementById('statutInput').value
        };

        fetch('http://127.0.0.1:8000/api/consultations', {
            method: 'POST',
            headers: getAuthHeaders(),
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess('Consultation créée avec succès');
                closeCreateConsultationModal();
                loadConsultations();
            } else {
                showError('Erreur lors de la création de la consultation');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showError('Erreur lors de la création de la consultation');
        });
    });

    document.getElementById('editConsultationForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const consultationId = document.getElementById('editConsultationId').value;
        const formData = {
            patient_id: document.getElementById('editPatientSelect').value,
            motif: document.getElementById('editMotifInput').value,
            montant: document.getElementById('editMontantInput').value,
            statut: document.getElementById('editStatutInput').value
        };

        fetch(`http://127.0.0.1:8000/api/consultations/${consultationId}`, {
            method: 'PUT',
            headers: getAuthHeaders(),
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess('Consultation modifiée avec succès');
                closeEditConsultationModal();
                loadConsultations();
            } else {
                showError(data.message || 'Erreur lors de la modification de la consultation');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showError('Erreur lors de la modification de la consultation');
        });
    });
}); 