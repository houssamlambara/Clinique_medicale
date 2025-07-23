function getAuthHeaders() {
    const token = localStorage.getItem('auth_token');
    return {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': token ? `Bearer ${token}` : '',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
    };
}

function loadRendezVous() {
    const userData = localStorage.getItem('user_data');
    if (!userData) return;

    const patientId = JSON.parse(userData).patient.id;
    
    fetch(`http://127.0.0.1:8000/api/rendezvous/patient/${patientId}`, {
        headers: getAuthHeaders()
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) displayRendezVous(data.data);
    })
    .catch(error => console.error('Erreur:', error));
}

function displayRendezVous(rendezvous) {
    const container = document.getElementById('rendezvous-list');
    const noRendezVous = document.getElementById('no-rendezvous');
    const errorMessage = document.getElementById('error-message');

    errorMessage.classList.add('hidden');
    noRendezVous.classList.add('hidden');
    container.innerHTML = '';

    if (rendezvous.length === 0) {
        noRendezVous.classList.remove('hidden');
        return;
    }

    rendezvous.forEach(rdv => {
        container.appendChild(createRendezVousElement(rdv));
    });
}

function createRendezVousElement(rdv) {
    const div = document.createElement('div');
    div.className = 'bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 border border-gray-100 p-6';

    const date = new Date(rdv.date_rdv).toLocaleDateString('fr-FR');
    const heure = rdv.heure_rdv ? rdv.heure_rdv.substring(0, 5) : '--:--';

    div.innerHTML = `
        <div class="flex justify-between items-start">
            <div class="flex-1">
                <div class="flex items-center mb-3">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-2 rounded-lg mr-3">
                        <i class="fas fa-calendar-check text-white text-sm"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">Rendez-vous #${rdv.id}</h4>
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-1">
                                <i class="fas fa-calendar text-blue-500 text-sm"></i>
                            <p class="text-sm text-gray-600">${date}</p>
                            </div>
                            <div class="flex items-center gap-1">
                                <i class="fas fa-clock text-green-500 text-sm"></i>
                                <p class="text-sm text-gray-600">${heure}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    ${rdv.patient ? `
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <p class="text-xs font-medium text-gray-600">Patient</p>
                            <p class="text-sm font-semibold text-gray-900">${rdv.patient.user.nom} ${rdv.patient.user.prenom}</p>
                        </div>
                    ` : ''}
                    
                    ${rdv.medecin ? `
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <p class="text-xs font-medium text-gray-600">Médecin</p>
                            <p class="text-sm font-semibold text-gray-900">Dr. ${rdv.medecin.user.nom} ${rdv.medecin.user.prenom}</p>
                        </div>
                    ` : ''}
                </div>
                
                ${rdv.raison ? `
                    <div class="text-sm text-gray-600">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span>${rdv.raison}</span>
                    </div>
                ` : ''}
            </div>
        </div>
    `;

    return div;
}

function showError(message) {
    document.getElementById('error-text').textContent = message;
    document.getElementById('error-message').classList.remove('hidden');
}

document.addEventListener('DOMContentLoaded', function () {
    loadRendezVous();
});