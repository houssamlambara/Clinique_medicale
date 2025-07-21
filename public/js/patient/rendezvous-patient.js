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

            const today = new Date();
            if (day.toDateString() === today.toDateString()) {
                dayElement.classList.add('bg-blue-100', 'text-blue-800');
            }

            if (selectedDate && day.toDateString() === selectedDate.toDateString()) {
                dayElement.classList.add('bg-blue-500', 'text-white');
            }

            if (day < today) {
                dayElement.classList.add('bg-gray-100', 'text-gray-400', 'cursor-not-allowed');
            } else {
                dayElement.addEventListener('click', () => selectDate(day));
            }
        } else {
            dayElement.classList.add('text-gray-400');
            dayElement.textContent = day.getDate();
        }

        grid.appendChild(dayElement);
    }
}

function selectDate(date) {
    selectedDate = date;
    updateCalendar();
    updateSelectedDateDisplay();
    checkCanConfirm();
}

function updateSelectedDateDisplay() {
    const element = document.getElementById('selectedDate');
    if (selectedDate) {
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        element.textContent = selectedDate.toLocaleDateString('fr-FR', options);
    } else {
        element.textContent = 'Veuillez sélectionner une date';
    }
}

function checkCanConfirm() {
    const button = document.getElementById('confirmButton');
    const summary = document.getElementById('appointmentSummary');
    
    if (selectedDate && selectedTime && selectedDoctor) {
        button.disabled = false;
        updateSummary();
        summary.classList.remove('hidden');
    } else {
        button.disabled = true;
        summary.classList.add('hidden');
    }
}

function updateSummary() {
    if (selectedDate && selectedTime && selectedDoctor) {
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('summaryDate').textContent = selectedDate.toLocaleDateString('fr-FR', options);
        document.getElementById('summaryTime').textContent = selectedTime;

        const doctor = doctors.find(d => d.id == selectedDoctor);
        if (doctor) {
            document.getElementById('summaryDoctor').textContent = `Dr. ${doctor.user.nom} ${doctor.user.prenom}`;
        }
    }
}

function initEventListeners() {
    // Gestion des créneaux horaires
    document.querySelectorAll('.time-slot').forEach(slot => {
        slot.addEventListener('click', function () {
            if (!this.classList.contains('disabled')) {
                document.querySelectorAll('.time-slot').forEach(s => s.classList.remove('selected'));
                this.classList.add('selected');
                selectedTime = this.dataset.time;
                checkCanConfirm();
            }
        });
    });

    // Gestion de la sélection du médecin
    document.getElementById('doctorSelect').addEventListener('change', function () {
        selectedDoctor = this.value;
        checkCanConfirm();
    });

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

    // Gestion de la confirmation (création/modification)
    document.getElementById('confirmButton').addEventListener('click', function () {
        if (selectedDate && selectedTime && selectedDoctor) {
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Confirmation...';

            const userData = JSON.parse(localStorage.getItem('user_data') || '{}');
            const patientId = userData.patient.id;

            const rendezVousData = {
                date_rdv: selectedDate.toISOString().split('T')[0], 
                heure_rdv: selectedTime,
                medecin_id: selectedDoctor,
                patient_id: patientId 
            };

            // Déterminer si c'est une création ou modification
            const editId = this.getAttribute('data-edit-id');
            const url = editId ? `http://127.0.0.1:8000/api/rendezvous/${editId}` : 'http://127.0.0.1:8000/api/rendezvous';
            const method = editId ? 'PUT' : 'POST';

            fetch(url, {
                method: method,
                headers: getAuthHeaders(),
                body: JSON.stringify(rendezVousData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const successMessage = document.getElementById('successMessage');
                    const message = editId ? 'Rendez-vous mis à jour avec succès' : 'Rendez-vous créé avec succès';
                    successMessage.querySelector('span').textContent = message;
                    successMessage.classList.remove('hidden');
                    
                    this.disabled = false;
                    this.innerHTML = '<i class="fas fa-check mr-2"></i>Confirmer le rendez-vous';
                    this.removeAttribute('data-edit-id');

                    setTimeout(() => {
                        closeAppointmentModal();
                        loadRendezVous();
                    }, 1500);

                    setTimeout(() => {
                        successMessage.classList.add('hidden');
                    }, 3000);
                } else {
                    throw new Error(data.message || 'Erreur lors de la création du rendez-vous');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-check mr-2"></i>Confirmer le rendez-vous';
                showError('Erreur lors de la création du rendez-vous: ' + error.message);
            });
        }
    });
}

// Initialisation de l'application
document.addEventListener('DOMContentLoaded', function () {
    initEventListeners();
    loadRendezVous();
});

function editRendezVous(rdvId) {
    fetch(`http://127.0.0.1:8000/api/rendezvous/${rdvId}`, {
        headers: getAuthHeaders()
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const rdv = data.data;
            
            selectedDate = new Date(rdv.date_rdv);
            selectedDoctor = rdv.medecin_id;
            selectedTime = rdv.heure_rdv ? rdv.heure_rdv.substring(0, 5) : '';
            
            openAppointmentModal();
            updateCalendar();
            updateSelectedDateDisplay();
            
            document.getElementById('doctorSelect').value = selectedDoctor;
            
            document.querySelectorAll('.time-slot').forEach(slot => {
                slot.classList.remove('selected');
                if (slot.dataset.time === selectedTime) {
                    slot.classList.add('selected');
                }
            });
            
            checkCanConfirm();
            
            document.getElementById('confirmButton').innerHTML = '<i class="fas fa-save mr-2"></i>Mettre à jour';
            document.getElementById('confirmButton').setAttribute('data-edit-id', rdvId);
            
        } else {
            showError('Erreur lors du chargement du rendez-vous');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showError('Erreur lors du chargement du rendez-vous');
    });
}

function deleteRendezVous(rdvId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce rendez-vous ?')) {
        fetch(`http://127.0.0.1:8000/api/rendezvous/${rdvId}`, {
            method: 'DELETE',
            headers: getAuthHeaders()
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const successMessage = document.getElementById('successMessage');
                successMessage.querySelector('span').textContent = 'Rendez-vous supprimé avec succès';
                successMessage.classList.remove('hidden');
                
                loadRendezVous();
                
                setTimeout(() => {
                    successMessage.classList.add('hidden');
                }, 3000);
                
            } else {
                throw new Error(data.message || 'Erreur lors de la suppression du rendez-vous');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showError('Erreur lors de la suppression du rendez-vous: ' + error.message);
        });
    }
}