// Variables globales pour stocker les données
let patients = [];
let medecins = [];
let selectedDate = null;
let selectedTime = null;
let selectedPatient = null;
let selectedDoctor = null;
let currentEditRendezVousId = null;
let currentMonth = new Date();

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    loadPatients();
    loadMedecins();
    loadRendezVous();
    initEventListeners();
    initCalendar();
});

function loadRendezVous() {
    fetch('http://127.0.0.1:8000/api/rendezvous', {
        headers: getAuthHeaders()
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayRendezVous(data.data);
        } else {
            showError(data.message || 'Erreur lors du chargement des rendez-vous');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showError('Erreur de connexion au serveur');
    });
}

function loadPatients() {
    fetch('http://127.0.0.1:8000/api/patients', {
        headers: getAuthHeaders()
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            patients = data.data;
            populatePatientSelects(data.data);
            if (document.getElementById('rendezvous-list').children.length === 0) {
                loadRendezVous();
            }
        }
    })
    .catch(error => console.error('Erreur:', error));
}

function loadMedecins() {
    fetch('http://127.0.0.1:8000/api/medecins', {
        headers: getAuthHeaders()
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            medecins = data.data;
            populateMedecinSelects(data.data);
            if (document.getElementById('rendezvous-list').children.length === 0) {
                loadRendezVous();
            }
        }
    })
    .catch(error => console.error('Erreur:', error));
}

function displayRendezVous(rendezvous) {
    const container = document.getElementById('rendezvous-list');
    const noRendezVous = document.getElementById('no-rendezvous');
    const errorMessage = document.getElementById('error-message');

    errorMessage.classList.add('hidden');
    
    if (rendezvous.length === 0) {
        container.innerHTML = '';
        noRendezVous.classList.remove('hidden');
        return;
    }

    noRendezVous.classList.add('hidden');
    container.innerHTML = rendezvous.map(rdv => createRendezVousElement(rdv)).join('');
}

function createRendezVousElement(rdv) {
    const patient = patients.find(p => p.id === rdv.patient_id);
    const medecin = medecins.find(m => m.id === rdv.medecin_id);
    
    const date = new Date(rdv.date_rdv);
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const formattedDate = date.toLocaleDateString('fr-FR', options);
    
    const statusClass = new Date(rdv.date_rdv) < new Date() ? 'bg-gray-500' : 'bg-green-500';
    const statusText = new Date(rdv.date_rdv) < new Date() ? 'Terminé' : 'À venir';
    
    const patientName = patient ? `${patient.user.prenom} ${patient.user.nom}` : 'Patient non trouvé';
    const medecinName = medecin ? `Dr. ${medecin.user.prenom} ${medecin.user.nom}` : 'Médecin non trouvé';

    return `
        <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full ${statusClass}"></div>
                            <span class="text-sm font-medium text-gray-600">${statusText}</span>
                        </div>
                        <span class="text-sm text-gray-500">#${rdv.id}</span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div class="flex items-center gap-3">
                            <div class="bg-blue-100 p-2 rounded-lg">
                                <i class="fas fa-calendar text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Date</p>
                                <p class="font-semibold text-gray-900">${formattedDate}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-3">
                            <div class="bg-green-100 p-2 rounded-lg">
                                <i class="fas fa-clock text-green-600"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Heure</p>
                                <p class="font-semibold text-gray-900">${rdv.heure_rdv ? rdv.heure_rdv.substring(0, 5) : 'Non définie'}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-3">
                            <div class="bg-purple-100 p-2 rounded-lg">
                                <i class="fas fa-user text-purple-600"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Patient</p>
                                <p class="font-semibold text-gray-900">${patientName}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-3">
                            <div class="bg-orange-100 p-2 rounded-lg">
                                <i class="fas fa-user-md text-orange-600"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Médecin</p>
                                <p class="font-semibold text-gray-900">${medecinName}</p>
                            </div>
                        </div>
                    </div>
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
        </div>
    `;
}

// Remplit les listes déroulantes des patients et médecins
function populatePatientSelects(patients) {
    const options = patients.map(patient => 
        `<option value="${patient.id}">${patient.user.nom} ${patient.user.prenom}</option>`
    ).join('');
    
    const defaultOption = '<option value="">Sélectionner un patient</option>';
    
    document.getElementById('patientSelect').innerHTML = defaultOption + options;
    document.getElementById('editPatientSelect').innerHTML = defaultOption + options;
}

function populateMedecinSelects(medecins) {
    const options = medecins.map(medecin => 
        `<option value="${medecin.id}">Dr. ${medecin.user.nom} ${medecin.user.prenom} - ${medecin.specialite}</option>`
    ).join('');
    
    const defaultOption = '<option value="">Sélectionner un médecin</option>';
    
    document.getElementById('medecinSelect').innerHTML = defaultOption + options;
    document.getElementById('editMedecinSelect').innerHTML = defaultOption + options;
}

function openAppointmentModal() {
    resetCalendarSelection();
    document.getElementById('appointmentModal').classList.remove('hidden');
}

function closeAppointmentModal() {
    document.getElementById('appointmentModal').classList.add('hidden');
    resetCalendarSelection();
}

function openEditAppointmentModal(rendezvous) {
    currentEditRendezVousId = rendezvous.id;
    selectedDate = rendezvous.date_rdv;
    selectedTime = rendezvous.heure_rdv ? rendezvous.heure_rdv.substring(0, 5) : '';
    selectedPatient = rendezvous.patient_id;
    selectedDoctor = rendezvous.medecin_id;
    
    document.getElementById('editPatientSelect').value = selectedPatient;
    document.getElementById('editMedecinSelect').value = selectedDoctor;
    
    initCalendar('edit');
    updateSelectedDateDisplay('edit');
    updateAppointmentSummary('edit');
    checkConfirmButton('edit');
    
    document.getElementById('editAppointmentModal').classList.remove('hidden');
}

function closeEditAppointmentModal() {
    document.getElementById('editAppointmentModal').classList.add('hidden');
}

function editRendezVous(rdvId) {
    fetch(`http://127.0.0.1:8000/api/rendezvous/${rdvId}`, {
        headers: getAuthHeaders()
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            openEditAppointmentModal(data.data);
        } else {
            showError(data.message || 'Erreur lors du chargement du rendez-vous');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showError('Erreur lors du chargement du rendez-vous');
    });
}

function updateRendezVous() {
    if (!selectedDate || !selectedTime || !selectedPatient || !selectedDoctor) {
        showError('Veuillez sélectionner une date, une heure, un patient et un médecin');
        return;
    }
    
    const formData = {
        patient_id: selectedPatient,
        medecin_id: selectedDoctor,
        date_rdv: selectedDate,
        heure_rdv: selectedTime
    };
    
    const confirmButton = document.getElementById('editConfirmButton');
    const originalText = confirmButton.innerHTML;
    confirmButton.disabled = true;
    confirmButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Modification...';
    
    fetch(`http://127.0.0.1:8000/api/rendezvous/${currentEditRendezVousId}`, {
        method: 'PUT',
        headers: getAuthHeaders(),
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        confirmButton.disabled = false;
        confirmButton.innerHTML = originalText;
        
        if (data.success) {
            showSuccess('Rendez-vous modifié avec succès');
            closeEditAppointmentModal();
            loadRendezVous();
        } else {
            showError(data.message || 'Erreur lors de la modification');
        }
    })
    .catch(error => {
        confirmButton.disabled = false;
        confirmButton.innerHTML = originalText;
        console.error('Erreur:', error);
        showError('Erreur lors de la modification');
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
                showSuccess('Rendez-vous supprimé avec succès');
                loadRendezVous();
            } else {
                showError(data.message || 'Erreur lors de la suppression');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showError('Erreur lors de la suppression');
        });
    }
}

// Configuration des événements des boutons
function initEventListeners() {
    document.getElementById('confirmButton').addEventListener('click', function() {
        if (!selectedDate || !selectedTime || !selectedPatient || !selectedDoctor) {
            showError('Veuillez sélectionner une date, une heure, un patient et un médecin');
            return;
        }
        
        const formData = {
            patient_id: selectedPatient,
            medecin_id: selectedDoctor,
            date_rdv: selectedDate,
            heure_rdv: selectedTime
        };
        
        const confirmButton = document.getElementById('confirmButton');
        confirmButton.disabled = true;
        confirmButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Confirmation...';
        
        fetch('http://127.0.0.1:8000/api/rendezvous', {
            method: 'POST',
            headers: getAuthHeaders(),
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            confirmButton.disabled = false;
            confirmButton.innerHTML = '<i class="fas fa-check mr-2"></i>Confirmer le rendez-vous';
            
            if (data.success) {
                showSuccess('Rendez-vous créé avec succès');
                closeAppointmentModal();
                loadRendezVous();
                resetCalendarSelection();
            } else {
                showError(data.message || 'Erreur lors de la création du rendez-vous');
            }
        })
        .catch(error => {
            confirmButton.disabled = false;
            confirmButton.innerHTML = '<i class="fas fa-check mr-2"></i>Confirmer le rendez-vous';
            console.error('Erreur:', error);
            showError('Erreur lors de la création du rendez-vous');
        });
    });
    
    document.getElementById('editConfirmButton').addEventListener('click', function() {
        updateRendezVous();
    });
}

// Fonctions utilitaires
function getAuthHeaders() {
    const token = localStorage.getItem('auth_token');
    return {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
    };
}

function showSuccess(message) {
    const successMessage = document.getElementById('successMessage');
    const successText = document.getElementById('success-text');
    successText.textContent = message;
    successMessage.classList.remove('hidden');
    
    setTimeout(() => {
        successMessage.classList.add('hidden');
    }, 3000);
}

function showError(message) {
    const errorMessage = document.getElementById('errorMessage');
    const errorText = document.getElementById('error-text-modal');
    errorText.textContent = message;
    errorMessage.classList.remove('hidden');
    
    setTimeout(() => {
        errorMessage.classList.add('hidden');
    }, 3000);
}

// Système de calendrier unifié (fonctionne pour ajout et modification)
function initCalendar(mode = 'add') {
    renderCalendar(mode);
    setupCalendarEventListeners(mode);
    setupTimeSlotEventListeners(mode);
    setupSelectEventListeners(mode);
}

function renderCalendar(mode = 'add') {
    const year = mode === 'edit' ? new Date(selectedDate).getFullYear() : currentMonth.getFullYear();
    const month = mode === 'edit' ? new Date(selectedDate).getMonth() : currentMonth.getMonth();
    
    const monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 
                       'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
    const monthElementId = mode === 'edit' ? 'editCurrentMonth' : 'currentMonth';
    document.getElementById(monthElementId).textContent = `${monthNames[month]} ${year}`;
    
    const firstDay = new Date(year, month, 1);
    const startDate = new Date(firstDay);
    startDate.setDate(startDate.getDate() - firstDay.getDay());
    
    const calendarGridId = mode === 'edit' ? 'editCalendarGrid' : 'calendarGrid';
    const calendarGrid = document.getElementById(calendarGridId);
    calendarGrid.innerHTML = '';
    
    for (let i = 0; i < 42; i++) {
        const date = new Date(startDate);
        date.setDate(startDate.getDate() + i);
        
        const dayElement = document.createElement('div');
        dayElement.className = 'text-center py-2 cursor-pointer hover:bg-gray-200 rounded-lg transition duration-200';
        
        if (date.getMonth() === month) {
            dayElement.textContent = date.getDate();
            dayElement.dataset.date = date.toISOString().split('T')[0];
            
            const today = new Date().toISOString().split('T')[0];
            if (date.toISOString().split('T')[0] === today) {
                dayElement.classList.add('bg-blue-100', 'font-semibold');
            }
            
            if (selectedDate === date.toISOString().split('T')[0]) {
                dayElement.classList.add('bg-blue-500', 'text-white');
            }
        } else {
            dayElement.textContent = date.getDate();
            dayElement.classList.add('text-gray-400');
        }
        
        calendarGrid.appendChild(dayElement);
    }
}

function setupCalendarEventListeners(mode = 'add') {
    const prevMonthId = mode === 'edit' ? 'editPrevMonth' : 'prevMonth';
    const nextMonthId = mode === 'edit' ? 'editNextMonth' : 'nextMonth';
    const calendarGridId = mode === 'edit' ? 'editCalendarGrid' : 'calendarGrid';
    
    document.getElementById(prevMonthId).addEventListener('click', function() {
        if (mode === 'edit') {
            const currentDate = new Date(selectedDate);
            currentDate.setMonth(currentDate.getMonth() - 1);
            selectedDate = currentDate.toISOString().split('T')[0];
        } else {
            currentMonth.setMonth(currentMonth.getMonth() - 1);
        }
        renderCalendar(mode);
        updateSelectedDateDisplay(mode);
        updateAppointmentSummary(mode);
        if (mode === 'add') checkConfirmButton(mode);
    });
    
    document.getElementById(nextMonthId).addEventListener('click', function() {
        if (mode === 'edit') {
            const currentDate = new Date(selectedDate);
            currentDate.setMonth(currentDate.getMonth() + 1);
            selectedDate = currentDate.toISOString().split('T')[0];
        } else {
            currentMonth.setMonth(currentMonth.getMonth() + 1);
        }
        renderCalendar(mode);
        updateSelectedDateDisplay(mode);
        updateAppointmentSummary(mode);
        if (mode === 'add') checkConfirmButton(mode);
    });
    
    document.getElementById(calendarGridId).addEventListener('click', function(e) {
        if (e.target.dataset.date) {
            selectedDate = e.target.dataset.date;
            renderCalendar(mode);
            updateSelectedDateDisplay(mode);
            updateAppointmentSummary(mode);
            checkConfirmButton(mode);
        }
    });
}

function setupTimeSlotEventListeners(mode = 'add') {
    const selector = mode === 'edit' ? '.edit-time-slot' : '.time-slot';
    
    document.querySelectorAll(selector).forEach(slot => {
        slot.addEventListener('click', function() {
            document.querySelectorAll(selector).forEach(s => {
                s.classList.remove('bg-blue-500', 'text-white');
                s.classList.add('border-gray-300');
            });
            
            this.classList.add('bg-blue-500', 'text-white');
            this.classList.remove('border-gray-300');
            
            selectedTime = this.dataset.time;
            updateAppointmentSummary(mode);
            checkConfirmButton(mode);
        });
    });
}

function setupSelectEventListeners(mode = 'add') {
    const patientSelectId = mode === 'edit' ? 'editPatientSelect' : 'patientSelect';
    const medecinSelectId = mode === 'edit' ? 'editMedecinSelect' : 'medecinSelect';
    
    document.getElementById(patientSelectId).addEventListener('change', function() {
        selectedPatient = this.value;
        updateAppointmentSummary(mode);
        checkConfirmButton(mode);
    });
    
    document.getElementById(medecinSelectId).addEventListener('change', function() {
        selectedDoctor = this.value;
        updateAppointmentSummary(mode);
        checkConfirmButton(mode);
    });
}

function updateSelectedDateDisplay(mode = 'add') {
    const selectedDateId = mode === 'edit' ? 'editSelectedDate' : 'selectedDate';
    const selectedDateElement = document.getElementById(selectedDateId);
    
    if (selectedDate) {
        const date = new Date(selectedDate);
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        selectedDateElement.textContent = date.toLocaleDateString('fr-FR', options);
    } else {
        selectedDateElement.textContent = 'Veuillez sélectionner une date';
    }
}

function updateAppointmentSummary(mode = 'add') {
    const summaryId = mode === 'edit' ? 'editAppointmentSummary' : 'appointmentSummary';
    const summaryDateId = mode === 'edit' ? 'editSummaryDate' : 'summaryDate';
    const summaryTimeId = mode === 'edit' ? 'editSummaryTime' : 'summaryTime';
    const summaryPatientId = mode === 'edit' ? 'editSummaryPatient' : 'summaryPatient';
    const summaryDoctorId = mode === 'edit' ? 'editSummaryDoctor' : 'summaryDoctor';
    
    const summary = document.getElementById(summaryId);
    const summaryDate = document.getElementById(summaryDateId);
    const summaryTime = document.getElementById(summaryTimeId);
    const summaryPatient = document.getElementById(summaryPatientId);
    const summaryDoctor = document.getElementById(summaryDoctorId);
    
    if (selectedDate && selectedTime && selectedPatient && selectedDoctor) {
        const date = new Date(selectedDate);
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        summaryDate.textContent = date.toLocaleDateString('fr-FR', options);
        summaryTime.textContent = selectedTime;
        
        const patient = patients.find(p => p.id == selectedPatient);
        summaryPatient.textContent = patient ? `${patient.user.prenom} ${patient.user.nom}` : '';
        
        const doctor = medecins.find(m => m.id == selectedDoctor);
        summaryDoctor.textContent = doctor ? `Dr. ${doctor.user.prenom} ${doctor.user.nom}` : '';
        
        summary.classList.remove('hidden');
    } else {
        summary.classList.add('hidden');
    }
}

function checkConfirmButton(mode = 'add') {
    const confirmButtonId = mode === 'edit' ? 'editConfirmButton' : 'confirmButton';
    const confirmButton = document.getElementById(confirmButtonId);
    
    if (selectedDate && selectedTime && selectedPatient && selectedDoctor) {
        confirmButton.disabled = false;
    } else {
        confirmButton.disabled = true;
    }
}

function resetCalendarSelection() {
    selectedDate = null;
    selectedTime = null;
    selectedPatient = null;
    selectedDoctor = null;
    
    document.getElementById('selectedDate').textContent = 'Veuillez sélectionner une date';
    document.getElementById('appointmentSummary').classList.add('hidden');
    document.getElementById('patientSelect').value = '';
    document.getElementById('medecinSelect').value = '';
    
    document.querySelectorAll('.time-slot').forEach(s => {
        s.classList.remove('bg-blue-500', 'text-white');
        s.classList.add('border-gray-300');
    });
    
    renderCalendar();
    checkConfirmButton();
}