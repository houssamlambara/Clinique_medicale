// Variables globales
let notifications = [];

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    loadNotifications();
});

// Charger les notifications du patient
async function loadNotifications() {
    try {
        const userData = localStorage.getItem('user_data');
        if (!userData) {
            showError('Erreur d\'authentification');
            return;
        }

        const user = JSON.parse(userData);
        const patientId = user.id;

        const response = await fetch(`/api/notifications/patient/${patientId}`, {
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
        console.error('Erreur:', error);
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
        <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-${getTypeColor(notification.type)}-500 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-start">
                <div class="flex-1">
                    <div class="flex items-center mb-2">
                        <i class="${getTypeIcon(notification.type)} text-${getTypeColor(notification.type)}-500 mr-2 text-lg"></i>
                        <span class="font-semibold text-gray-900">${getTypeLabel(notification.type)}</span>
                        <span class="text-sm text-gray-500 ml-2">• ${formatDate(notification.created_at)}</span>
                    </div>
                    <p class="text-gray-700 mb-2 leading-relaxed">${notification.message}</p>
                </div>
            </div>
        </div>
    `).join('');
}

// Utilitaires
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