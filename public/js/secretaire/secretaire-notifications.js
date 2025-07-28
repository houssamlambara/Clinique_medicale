// Variables globales
let patients = [];
let notifications = [];

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    loadPatients();
    loadNotifications();
    setupEventListeners();
});

// Configuration des événements
function setupEventListeners() {
    document.getElementById('sendNotificationForm').addEventListener('submit', function(e) {
        e.preventDefault();
        sendNotification();
    });
}

// Charger les patients
async function loadPatients() {
    try {
        const response = await fetch('/api/patients', {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'Content-Type': 'application/json'
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            patients = data.data || [];
            populatePatientSelect();
        } else {
            showError('Erreur lors du chargement des patients');
        }
    } catch (error) {
        showError('Erreur de connexion');
    }
}

// Remplir le select des patients
function populatePatientSelect() {
    const select = document.getElementById('notificationPatient');
    
    if (!select) {
        return;
    }
    
    select.innerHTML = '<option value="">Sélectionner un patient</option>';
    
    if (patients && patients.length > 0) {
        patients.forEach(patient => {
            const option = document.createElement('option');
            option.value = patient.id;
            option.textContent = `${patient.user.nom} ${patient.user.prenom}`;
            select.appendChild(option);
        });
    } else {
        const option = document.createElement('option');
        option.value = "";
        option.textContent = "Aucun patient disponible";
        option.disabled = true;
        select.appendChild(option);
    }
}

// Charger les notifications
async function loadNotifications() {
    try {
        const response = await fetch('/api/notifications', {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'Content-Type': 'application/json'
            }
        });

        if (response.ok) {
            const data = await response.json();
            notifications = data.data;
            renderNotifications();
        } else {
            showError('Erreur lors du chargement des notifications');
        }
    } catch (error) {
        showError('Erreur de connexion');
    }
}

// Afficher les notifications
function renderNotifications() {
    const container = document.getElementById('notifications-list');
    const noNotifications = document.getElementById('no-notifications');
    const errorMessage = document.getElementById('error-message');

    if (notifications.length === 0) {
        container.innerHTML = '';
        noNotifications.classList.remove('hidden');
        errorMessage.classList.add('hidden');
        return;
    }

    noNotifications.classList.add('hidden');
    errorMessage.classList.add('hidden');

    container.innerHTML = notifications.map(notification => `
        <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-${getTypeColor(notification.type)}-500">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="flex items-center mb-2">
                        <i class="${getTypeIcon(notification.type)} text-${getTypeColor(notification.type)}-500 mr-2"></i>
                        <span class="font-semibold text-gray-900">${getTypeLabel(notification.type)}</span>
                        <span class="text-sm text-gray-500 ml-2">• ${formatDate(notification.created_at)}</span>
                    </div>
                    <p class="text-gray-700 mb-2">${notification.message}</p>
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-500">
                            <i class="fas fa-user mr-1"></i>
                            ${notification.patient && notification.patient.user ? `${notification.patient.user.nom} ${notification.patient.user.prenom}` : 'Patient inconnu'}
                        </p>
                        <div class="flex items-center">
                            ${notification.email_sent ? 
                                '<span class="text-green-600 text-sm"><i class="fas fa-check-circle mr-1"></i>Email envoyé</span>' : 
                                '<span class="text-red-600 text-sm"><i class="fas fa-times-circle mr-1"></i>Email échoué</span>'
                            }
                        </div>
                    </div>
                </div>
                <button onclick="deleteNotification(${notification.id})" class="text-red-500 hover:text-red-700 ml-4">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `).join('');
}

// Envoyer une notification
async function sendNotification() {
    const patientId = document.getElementById('notificationPatient').value;
    const type = document.getElementById('notificationType').value;
    const message = document.getElementById('notificationMessage').value;

    if (!patientId || !type || !message.trim()) {
        showError('Veuillez remplir tous les champs');
        return;
    }

    try {
        const response = await fetch('/api/notifications', {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                patient_id: patientId,
                type: type,
                message: message.trim()
            })
        });

        if (response.ok) {
            const data = await response.json();
            showSuccess(data.message);
            closeModal('sendNotificationModal');
            resetForm('sendNotificationForm');
            loadNotifications();
        } else {
            const error = await response.json();
            showError(error.message || 'Erreur lors de l\'envoi');
        }
    } catch (error) {
        showError('Erreur de connexion');
    }
}

// Supprimer une notification
async function deleteNotification(id) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cette notification ?')) {
        return;
    }

    try {
        const response = await fetch(`/api/notifications/${id}`, {
            method: 'DELETE',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'Content-Type': 'application/json'
            }
        });

        if (response.ok) {
            const data = await response.json();
            showSuccess(data.message);
            loadNotifications();
        } else {
            const error = await response.json();
            showError(error.message || 'Erreur lors de la suppression');
        }
    } catch (error) {
        showError('Erreur de connexion');
    }
}

// Gestion des modals
function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

function openSendNotificationModal() {
    openModal('sendNotificationModal');
}

function closeSendNotificationModal() {
    closeModal('sendNotificationModal');
}

// Reset des formulaires
function resetForm(formId) {
    document.getElementById(formId).reset();
}

// Utilitaires pour les types de notifications
function getTypeLabel(type) {
    const labels = {
        'rendezvous': 'Rendez-vous',
        'consultation': 'Consultation',
        'resultat': 'Résultat',
        'information': 'Information',
        'rappel': 'Rappel'
    };
    return labels[type] || 'Notification';
}

function getTypeIcon(type) {
    const icons = {
        'rendezvous': 'fas fa-calendar-check',
        'consultation': 'fas fa-stethoscope',
        'resultat': 'fas fa-file-medical',
        'information': 'fas fa-info-circle',
        'rappel': 'fas fa-bell'
    };
    return icons[type] || 'fas fa-bell';
}

function getTypeColor(type) {
    const colors = {
        'rendezvous': 'blue',
        'consultation': 'green',
        'resultat': 'purple',
        'information': 'gray',
        'rappel': 'orange'
    };
    return colors[type] || 'gray';
}

// Formatage de date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Messages
function showSuccess(message) {
    const successMessage = document.getElementById('successMessage');
    const successText = document.getElementById('successText');
    successText.textContent = message;
    successMessage.classList.remove('hidden');
    
    setTimeout(() => {
        successMessage.classList.add('hidden');
    }, 3000);
}

function showError(message) {
    const errorMessage = document.getElementById('errorMessage');
    const errorText = document.getElementById('errorText');
    errorText.textContent = message;
    errorMessage.classList.remove('hidden');
    
    setTimeout(() => {
        errorMessage.classList.add('hidden');
    }, 3000);
}