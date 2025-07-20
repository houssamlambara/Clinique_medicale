let currentMedecin = null;
let patientsCache = null;

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
        loadPatients();
    } else {
        showError('Aucun utilisateur connecté');
    }
}

function loadPatients() {
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
            patientsCache = data.data;
            displayPatients(data.data);
        } else {
            showError(data.message || 'Erreur lors du chargement des patients');
        }
    })
    .catch(error => {
        showError('Erreur de connexion au serveur: ' + error.message);
    });
}

function displayPatients(patients) {
    const container = document.getElementById('patients-list');
    const noPatients = document.getElementById('no-patients');
    const errorMessage = document.getElementById('error-message');

    noPatients.classList.add('hidden');
    errorMessage.classList.add('hidden');

    if (!patients || patients.length === 0) {
        container.innerHTML = '';
        noPatients.classList.remove('hidden');
        return;
    }

    let html = '';
    patients.forEach(patient => {
        html += `
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="bg-gradient-to-r from-purple-500 to-indigo-600 p-3 rounded-xl">
                            <i class="fas fa-user text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">${patient.user.nom} ${patient.user.prenom}</h3>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <tbody>
                                <tr class="border-b border-gray-200">
                                    <td class="py-2 px-2 font-medium text-gray-600 w-1/3">Date de naissance:</td>
                                    <td class="py-2 px-2 text-gray-900">${patient.date_naissance ? new Date(patient.date_naissance).toLocaleDateString('fr-FR') : 'Non spécifiée'}</td>
                                </tr>
                                <tr class="border-b border-gray-200">
                                    <td class="py-2 px-2 font-medium text-gray-600 w-1/3">Genre:</td>
                                    <td class="py-2 px-2 text-gray-900">${patient.genre || 'Non spécifié'}</td>
                                </tr>
                                <tr class="border-b border-gray-200">
                                    <td class="py-2 px-2 font-medium text-gray-600 w-1/3">Email:</td>
                                    <td class="py-2 px-2 text-gray-900">${patient.user.email || 'Non spécifié'}</td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-2 font-medium text-gray-600 w-1/3">Téléphone:</td>
                                    <td class="py-2 px-2 text-gray-900">${patient.user.telephone || 'Non spécifié'}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
}

function viewPatientRendezVous(patientId) {
    showSuccess('Fonctionnalité des rendez-vous à implémenter');
}

function viewPatientConsultations(patientId) {
    showSuccess('Fonctionnalité des consultations à implémenter');
}

function viewPatientDossier(patientId) {
    showSuccess('Fonctionnalité du dossier médical à implémenter');
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
    const noPatients = document.getElementById('no-patients');
    const patientsList = document.getElementById('patients-list');

    errorText.textContent = message;
    errorMessage.classList.remove('hidden');
    noPatients.classList.add('hidden');
    patientsList.innerHTML = '';
} 