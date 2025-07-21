let currentPatient = null;
let prescriptionsCache = null;

document.addEventListener('DOMContentLoaded', function () {
    loadUserData();
});

function loadUserData() {
    const userData = localStorage.getItem('user_data');

    if (userData) {
        currentPatient = JSON.parse(userData);

        if (currentPatient.role !== 'patient') {
            showError('Accès non autorisé');
            return;
        }

        loadPrescriptions();
    } else {
        showError('Aucun utilisateur connecté');
    }
}

function loadPrescriptions() {
    console.log('Chargement des prescriptions...');

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

    console.log('Patient ID:', currentPatient.patient.id);
    console.log('URL API:', `http://127.0.0.1:8000/api/prescriptions/patient/${currentPatient.patient.id}`);

    fetch(`http://127.0.0.1:8000/api/prescriptions/patient/${currentPatient.patient.id}`, {
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
                prescriptionsCache = data.data;
                console.log('Prescriptions trouvées:', data.data.length);
                displayPrescriptions(data.data);
            } else {
                console.log('Erreur API:', data.message);
                showError(data.message || 'Erreur lors du chargement des prescriptions');
            }
        })
        .catch(error => {
            console.log('Erreur fetch:', error);
            showError('Erreur de connexion au serveur: ' + error.message);
        });
}

function displayPrescriptions(prescriptions) {
    const container = document.getElementById('prescriptions-list');
    const noPrescriptions = document.getElementById('no-prescriptions');
    const errorMessage = document.getElementById('error-message');
    const loading = document.getElementById('loading');

    loading.classList.add('hidden');
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
        const medecinName = prescription.medecin?.user ? 
            `Dr. ${prescription.medecin.user.nom} ${prescription.medecin.user.prenom}` : 
            'Médecin inconnu';
        const patientName = `${currentPatient.prenom} ${currentPatient.nom}`;

        html += `
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-3 rounded-xl">
                            <i class="fas fa-pills text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Prescription #${prescription.id}</h3>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <!-- Informations de la prescription -->
                    <div class="space-y-4">
                        <h4 class="text-md font-semibold text-gray-900 mb-3">
                            <i class="fas fa-info-circle mr-2 text-blue-500"></i>Informations de la prescription
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
                        <i class="fas fa-pills mr-2 text-indigo-500"></i>Médicament
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

function logout() {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user_data');
    window.location.href = '/login';
}

function showSuccess(message) {
    const successMessage = document.getElementById('success-message');
    const successText = document.getElementById('success-text');
    successText.textContent = message;
    successMessage.classList.remove('hidden');

    setTimeout(() => {
        successMessage.classList.add('hidden');
    }, 5000);
}

function showError(message) {
    const errorMessage = document.getElementById('error-message');
    const errorText = document.getElementById('error-text');
    errorText.textContent = message;
    errorMessage.classList.remove('hidden');

    setTimeout(() => {
        errorMessage.classList.add('hidden');
    }, 5000);
} 