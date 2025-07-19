let usersCache = {};

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
    if (!userData) return;

    const patientId = JSON.parse(userData).patient.id;
    
    fetch(`http://127.0.0.1:8000/api/consultations/patient/${patientId}`, {
        headers: getAuthHeaders()
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) displayConsultations(data.data);
    })
    .catch(error => {
        console.error('Erreur:', error);
        showError('Erreur lors du chargement des consultations');
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
                
                ${consultation.medecin ? `
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-user-md text-green-500 mr-2"></i>
                            <p class="text-xs font-medium text-gray-600">Médecin</p>
                        </div>
                        <p class="text-sm font-semibold text-gray-900" id="medecin-${consultation.id}">Chargement...</p>
                        <p class="text-xs text-gray-500">Spécialité: ${consultation.medecin.specialite}</p>
                    </div>
                ` : '<div class="bg-gray-50 p-4 rounded-lg border border-gray-200"><p class="text-sm text-gray-500">Médecin: Données non disponibles</p></div>'}
                
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-euro-sign text-green-500 mr-2"></i>
                        <p class="text-xs font-medium text-gray-600">Montant</p>
                    </div>
                    <p class="text-sm font-semibold text-gray-900">${consultation.montant} €</p>
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

    if (consultation.medecin) {
        getUserData(consultation.medecin.user_id)
            .then(medecinUser => {
                const medecinElement = document.getElementById(`medecin-${consultation.id}`);
                if (medecinElement) {
                    medecinElement.textContent = medecinUser ? `Dr. ${medecinUser.nom} ${medecinUser.prenom}` : 'Non disponible';
                }
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

document.addEventListener('DOMContentLoaded', function () {
    loadConsultations();
}); 