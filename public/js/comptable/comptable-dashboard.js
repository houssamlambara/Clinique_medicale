let currentComptable = null;

document.addEventListener('DOMContentLoaded', function () {
    loadUserData();
    loadStatistics();
});

function loadUserData() {
    const userData = localStorage.getItem('user_data');
    
    if (userData) {
        currentComptable = JSON.parse(userData);
        
        if (currentComptable.role !== 'comptable') {
            showError('Accès non autorisé');
            return;
        }
        
        document.getElementById('comptable-name').textContent = `${currentComptable.prenom} ${currentComptable.nom}`;
    } else {
        showError('Aucun utilisateur connecté');
    }
}

function loadStatistics() {
    const token = localStorage.getItem('auth_token');
    if (!token) {
        showError('Token d\'authentification manquant');
        return;
    }

    // Charger les statistiques des factures
    fetch('http://127.0.0.1:8000/api/factures', {
        method: 'GET',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateFactureStats(data.data);
        }
    })
    .catch(error => {
        console.log('Erreur chargement factures:', error);
    });

    // Charger les statistiques des dépenses
    fetch('http://127.0.0.1:8000/api/depenses', {
        method: 'GET',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateDepenseStats(data.data);
        }
    })
    .catch(error => {
        console.log('Erreur chargement dépenses:', error);
    });
}

function updateFactureStats(factures) {
    const totalFactures = factures.length;
    const facturesPayees = factures.filter(f => f.est_paye).length;
    const totalMontant = factures.reduce((sum, f) => sum + parseFloat(f.montant), 0);
    
    document.getElementById('total-factures').textContent = totalFactures;
    document.getElementById('factures-payees').textContent = facturesPayees;
    
    // Mettre à jour le solde net (sera calculé avec les dépenses)
    updateSoldeNet(totalMontant);
}

function updateDepenseStats(depenses) {
    const totalDepenses = depenses.length;
    const totalMontant = depenses.reduce((sum, d) => sum + parseFloat(d.montant), 0);
    
    document.getElementById('total-depenses').textContent = totalDepenses;
    
    // Mettre à jour le solde net
    updateSoldeNet(null, totalMontant);
}

function updateSoldeNet(revenus, depenses) {
    // Cette fonction sera appelée avec les revenus et dépenses
    // Pour l'instant, on affiche un solde fictif
    const soldeNet = 15000; // Exemple
    document.getElementById('solde-net').textContent = `${soldeNet.toLocaleString()} €`;
}

function loadNotifications() {
    if (!currentComptable) return;

    // Simulation de notifications financières
    const notifications = [
        {
            id: 1,
            type: 'facture',
            message: '5 factures en attente de paiement',
            date: new Date().toLocaleDateString('fr-FR'),
            icon: 'fas fa-file-invoice',
            color: 'text-blue-600'
        },
        {
            id: 2,
            type: 'depense',
            message: 'Nouvelle dépense enregistrée: Fournitures médicales',
            date: new Date().toLocaleDateString('fr-FR'),
            icon: 'fas fa-money-bill-wave',
            color: 'text-red-600'
        },
        {
            id: 3,
            type: 'paiement',
            message: 'Paiement reçu: Facture #1234',
            date: new Date().toLocaleDateString('fr-FR'),
            icon: 'fas fa-check-circle',
            color: 'text-green-600'
        }
    ];

    displayNotifications(notifications);
}

function displayNotifications(notifications) {
    const container = document.getElementById('notifications-list');
    const noNotifications = document.getElementById('no-notifications');

    noNotifications.classList.add('hidden');
    container.innerHTML = '';

    if (notifications.length === 0) {
        noNotifications.classList.remove('hidden');
        return;
    }

    notifications.forEach(notification => {
        const div = document.createElement('div');
        div.className = 'flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors';

        div.innerHTML = `
            <div class="flex-shrink-0 mr-4">
                <i class="${notification.icon} ${notification.color} text-xl"></i>
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-900">${notification.message}</p>
                <p class="text-xs text-gray-500">${notification.date}</p>
            </div>
        `;

        container.appendChild(div);
    });
}

function logout() {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user_data');
    window.location.href = '/login';
}

function showError(message) {
    alert('Erreur: ' + message);
} 