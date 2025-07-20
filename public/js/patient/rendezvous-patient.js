// Variables globales
let currentDate = new Date();
let selectedDate = null;
let selectedTime = null;
let selectedDoctor = null;
let doctors = [];

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
                        <div class="flex items-center gap-2">
                            <p class="text-sm text-gray-600">${date}</p>
                            <span class="text-xs text-gray-500">•</span>
                            <p class="text-sm font-medium text-blue-600">
                                <i class="fas fa-clock mr-1"></i>${heure}
                            </p>
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
            
            <div class="flex flex-col gap-2 ml-4">
                <button onclick="editRendezVous(${rdv.id})" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center gap-2">
                    <i class="fas fa-edit"></i>
                    Modifier
                </button>
                <button onclick="deleteRendezVous(${rdv.id})" 
                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center gap-2">
                    <i class="fas fa-trash"></i>
                    Supprimer
                </button>
            </div>
        </div>
    `;

    return div;
}

function showError(message) {
    document.getElementById('error-text').textContent = message;
    document.getElementById('error-message').classList.remove('hidden');
}

function loadDoctors() {
    fetch('http://127.0.0.1:8000/api/medecins', {
        headers: getAuthHeaders()
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data.length > 0) {
            doctors = data.data;
            populateDoctorSelect();
        }
    })
    .catch(error => {
        console.error('Erreur lors du chargement des médecins:', error);
        populateDoctorSelect();
    });
}

function populateDoctorSelect() {
    const select = document.getElementById('doctorSelect');
    select.innerHTML = '<option value="">Sélectionnez un médecin</option>';
    
    doctors.forEach(doctor => {
        const option = document.createElement('option');
        option.value = doctor.id;
        option.textContent = `Dr. ${doctor.user.nom} ${doctor.user.prenom} - ${doctor.specialite}`;
        select.appendChild(option);
    });
}

function openAppointmentModal() {
    document.getElementById('appointmentModal').classList.remove('hidden');
    initCalendar();
    loadDoctors();
}

function closeAppointmentModal() {
    document.getElementById('appointmentModal').classList.add('hidden');
    selectedDate = null;
    selectedTime = null;
    selectedDoctor = null;
    document.querySelectorAll('.time-slot').forEach(slot => slot.classList.remove('selected'));
    document.getElementById('appointmentSummary').classList.add('hidden');
    document.getElementById('confirmButton').disabled = true;
    document.getElementById('doctorSelect').value = '';
}

function initCalendar() {
    updateCalendar();
    updateMonthDisplay();
}

function updateMonthDisplay() {
    const options = { year: 'numeric', month: 'long' };
    document.getElementById('currentMonth').textContent = currentDate.toLocaleDateString('fr-FR', options);
}

function updateCalendar() {
    const grid = document.getElementById('calendarGrid');
    grid.innerHTML = '';

    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    const firstDay = new Date(year, month, 1);
    const startDate = new Date(firstDay);
    startDate.setDate(startDate.getDate() - firstDay.getDay());

    for (let i = 0; i < 42; i++) {
        const day = new Date(startDate);
        day.setDate(startDate.getDate() + i);

        const dayElement = document.createElement('div');
        dayElement.className = 'calendar-day p-2 text-center border border-gray-200 rounded-lg cursor-pointer hover:bg-blue-50 transition-colors';

        if (day.getMonth() === month) {
            dayElement.textContent = day.getDate();
            
            // Désactiver les jours passés
            if (day < new Date().setHours(0, 0, 0, 0)) {
                dayElement.classList.add('text-gray-400', 'cursor-not-allowed');
                dayElement.classList.remove('hover:bg-blue-50');
            } else {
                dayElement.onclick = () => selectDate(day);
            }
        } else {
            dayElement.classList.add('text-gray-300');
        }

        grid.appendChild(dayElement);
    }
}

function selectDate(date) {
    selectedDate = date;
    document.querySelectorAll('.calendar-day').forEach(day => day.classList.remove('bg-blue-500', 'text-white'));
    event.target.classList.add('bg-blue-500', 'text-white');
    updateSelectedDateDisplay();
    checkCanConfirm();
}

function updateSelectedDateDisplay() {
    const display = document.getElementById('selectedDate');
    if (selectedDate) {
        display.textContent = selectedDate.toLocaleDateString('fr-FR', { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
    } else {
        display.textContent = 'Veuillez sélectionner une date';
    }
}

function checkCanConfirm() {
    const confirmButton = document.getElementById('confirmButton');
    const canConfirm = selectedDate && selectedTime && selectedDoctor;
    confirmButton.disabled = !canConfirm;
    
    if (canConfirm) {
        updateSummary();
        document.getElementById('appointmentSummary').classList.remove('hidden');
    } else {
        document.getElementById('appointmentSummary').classList.add('hidden');
    }
}

function updateSummary() {
    if (selectedDate && selectedTime && selectedDoctor) {
        const doctor = doctors.find(d => d.id == selectedDoctor);
        document.getElementById('summaryDate').textContent = selectedDate.toLocaleDateString('fr-FR');
        document.getElementById('summaryTime').textContent = selectedTime;
        document.getElementById('summaryDoctor').textContent = `Dr. ${doctor.user.nom} ${doctor.user.prenom}`;
    }
}

function initEventListeners() {
    // Navigation du calendrier
    document.getElementById('prevMonth').addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        updateCalendar();
        updateMonthDisplay();
    });

    document.getElementById('nextMonth').addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        updateCalendar();
        updateMonthDisplay();
    });

    // Sélection du médecin
    document.getElementById('doctorSelect').addEventListener('change', (e) => {
        selectedDoctor = e.target.value;
        checkCanConfirm();
    });

    // Sélection des créneaux horaires
    document.querySelectorAll('.time-slot').forEach(slot => {
        slot.addEventListener('click', () => {
            document.querySelectorAll('.time-slot').forEach(s => s.classList.remove('selected', 'bg-blue-500', 'text-white'));
            slot.classList.add('selected', 'bg-blue-500', 'text-white');
            selectedTime = slot.dataset.time;
            checkCanConfirm();
        });
    });

    // Confirmation du rendez-vous
    document.getElementById('confirmButton').addEventListener('click', () => {
        if (selectedDate && selectedTime && selectedDoctor) {
            createRendezVous();
        }
    });

    // Formulaire de création
    document.getElementById('appointmentForm')?.addEventListener('submit', (e) => {
        e.preventDefault();
        createRendezVous();
    });
}

function createRendezVous() {
    const userData = localStorage.getItem('user_data');
    if (!userData) {
        showError('Utilisateur non connecté');
        return;
    }

    const patientId = JSON.parse(userData).patient.id;
    const dateTime = new Date(selectedDate);
    const [hours, minutes] = selectedTime.split(':');
    dateTime.setHours(parseInt(hours), parseInt(minutes), 0, 0);

    const rendezVousData = {
        patient_id: patientId,
        medecin_id: selectedDoctor,
        date_rdv: dateTime.toISOString(),
        heure_rdv: selectedTime,
        raison: document.getElementById('raisonInput')?.value || 'Consultation'
    };

    fetch('http://127.0.0.1:8000/api/rendezvous', {
        method: 'POST',
        headers: getAuthHeaders(),
        body: JSON.stringify(rendezVousData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccess('Rendez-vous créé avec succès !');
            closeAppointmentModal();
            loadRendezVous();
        } else {
            showError(data.message || 'Erreur lors de la création du rendez-vous');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showError('Erreur de connexion au serveur');
    });
}

function editRendezVous(rdvId) {
    // Charger les données du rendez-vous
    fetch(`http://127.0.0.1:8000/api/rendezvous/${rdvId}`, {
        headers: getAuthHeaders()
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const rdv = data.data;
            // Remplir le formulaire d'édition
            document.getElementById('editRendezVousId').value = rdvId;
            document.getElementById('editDateInput').value = rdv.date_rdv.split('T')[0];
            document.getElementById('editTimeInput').value = rdv.heure_rdv || '';
            document.getElementById('editDoctorSelect').value = rdv.medecin_id || '';
            document.getElementById('editRaisonInput').value = rdv.raison || '';
            
            // Afficher le modal d'édition
            document.getElementById('editAppointmentModal').classList.remove('hidden');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showError('Erreur lors du chargement du rendez-vous');
    });
}

function updateRendezVous() {
    const rdvId = document.getElementById('editRendezVousId').value;
    const dateInput = document.getElementById('editDateInput').value;
    const timeInput = document.getElementById('editTimeInput').value;
    const doctorId = document.getElementById('editDoctorSelect').value;
    const raison = document.getElementById('editRaisonInput').value;

    if (!dateInput || !timeInput || !doctorId) {
        showError('Veuillez remplir tous les champs obligatoires');
        return;
    }

    const dateTime = new Date(dateInput + 'T' + timeInput);

    const updateData = {
        date_rdv: dateTime.toISOString(),
        heure_rdv: timeInput,
        medecin_id: doctorId,
        raison: raison
    };

    fetch(`http://127.0.0.1:8000/api/rendezvous/${rdvId}`, {
        method: 'PUT',
        headers: getAuthHeaders(),
        body: JSON.stringify(updateData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccess('Rendez-vous mis à jour avec succès !');
            closeEditAppointmentModal();
            loadRendezVous();
        } else {
            showError(data.message || 'Erreur lors de la mise à jour');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showError('Erreur de connexion au serveur');
    });
}

function deleteRendezVous(rdvId) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer ce rendez-vous ?')) {
        return;
    }

    fetch(`http://127.0.0.1:8000/api/rendezvous/${rdvId}`, {
        method: 'DELETE',
        headers: getAuthHeaders()
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccess('Rendez-vous supprimé avec succès !');
            loadRendezVous();
        } else {
            showError(data.message || 'Erreur lors de la suppression');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showError('Erreur de connexion au serveur');
    });
}

function closeEditAppointmentModal() {
    document.getElementById('editAppointmentModal').classList.add('hidden');
}

function showSuccess(message) {
    const successMessage = document.getElementById('successMessage');
    successMessage.querySelector('span').textContent = message;
    successMessage.classList.remove('hidden');
    
    setTimeout(() => {
        successMessage.classList.add('hidden');
    }, 3000);
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    loadRendezVous();
    initEventListeners();
}); 