// Variables globales
let currentComptable = null;
let depenses = [];

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function () {
    loadUserData();
    loadDepenses();
});

// Charger les données de l'utilisateur connecté
function loadUserData() {
    const userData = localStorage.getItem('user_data');
    
    if (!userData) {
        return;
    }
    
    currentComptable = JSON.parse(userData);
    
    if (currentComptable.role !== 'comptable') {
        return;
    }
    
    // Afficher le nom du comptable
    const comptableNameElement = document.getElementById('comptable-name');
    if (comptableNameElement) {
        comptableNameElement.textContent = `${currentComptable.prenom} ${currentComptable.nom}`;
    }
}

// Charger toutes les dépenses depuis l'API
function loadDepenses() {
    const token = localStorage.getItem('auth_token');
    if (!token) {
        showError('Token d\'authentification manquant');
        return;
    }

    // Appel API pour récupérer les dépenses
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
            depenses = data.data;
            displayDepenses(depenses);
        } else {
            showError(data.message || 'Erreur lors du chargement des dépenses');
        }
    })
    .catch(error => {
        showError('Erreur de connexion au serveur');
    });
}

// Afficher la liste des dépenses
function displayDepenses(depensesToDisplay) {
    const container = document.getElementById('depenses-list');
    const noDepenses = document.getElementById('no-depenses');

    // Vider le conteneur
    container.innerHTML = '';

    // Si aucune dépense, afficher le message
    if (depensesToDisplay.length === 0) {
        noDepenses.classList.remove('hidden');
        return;
    }

    // Cacher le message "aucune dépense"
    noDepenses.classList.add('hidden');

    // Créer une carte pour chaque dépense
    depensesToDisplay.forEach(depense => {
        const depenseCard = createDepenseCard(depense);
        container.appendChild(depenseCard);
    });
}

// Créer une carte HTML pour une dépense
function createDepenseCard(depense) {
    const div = document.createElement('div');
    div.className = 'bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow';
    
    // Déterminer le statut de paiement
    const isPayee = depense.est_paye;
    const statusClass = isPayee ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
    const statusText = isPayee ? 'Payée' : 'Non payée';
    
    // Formater les dates
    const dateDepense = new Date(depense.date_depense).toLocaleDateString('fr-FR');
    const datePaiement = depense.date_paiement ? new Date(depense.date_paiement).toLocaleDateString('fr-FR') : 'Non payée';
    const montant = parseFloat(depense.montant).toLocaleString();
    
    div.innerHTML = `
        <div class="flex justify-between items-start mb-4">
            <div>
                <h4 class="text-lg font-semibold text-gray-900">Dépense #${depense.id}</h4>
                <p class="text-sm text-gray-600">${depense.description}</p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="px-3 py-1 rounded-full text-xs font-medium ${statusClass}">${statusText}</span>
                <div class="flex space-x-2">
                    <button onclick="editDepense(${depense.id})" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition-colors">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteDepense(${depense.id})" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition-colors">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <p class="text-sm font-medium text-gray-700">Montant</p>
                <p class="text-lg font-bold text-red-600">${montant} €</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-700">Catégorie</p>
                <p class="text-sm text-gray-600">${depense.categorie}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-700">Date de dépense</p>
                <p class="text-sm text-gray-600">${dateDepense}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-700">Date de paiement</p>
                <p class="text-sm text-gray-600">${datePaiement}</p>
            </div>
        </div>
    `;
    
    return div;
}

// Filtrer les dépenses selon les critères
function filterDepenses() {
    // Récupérer les valeurs des filtres
    const statusFilter = document.getElementById('filter-status').value;
    const categorieFilter = document.getElementById('filter-categorie').value;
    const dateStartFilter = document.getElementById('filter-date-start').value;
    const dateEndFilter = document.getElementById('filter-date-end').value;

    let filteredDepenses = depenses;

    // Filtrer par statut
    if (statusFilter) {
        filteredDepenses = filteredDepenses.filter(depense => {
            if (statusFilter === 'paye') return depense.est_paye;
            if (statusFilter === 'non-paye') return !depense.est_paye;
            return true;
        });
    }

    // Filtrer par catégorie
    if (categorieFilter) {
        filteredDepenses = filteredDepenses.filter(depense => depense.categorie === categorieFilter);
    }

    // Filtrer par date de début
    if (dateStartFilter) {
        filteredDepenses = filteredDepenses.filter(depense => depense.date_depense >= dateStartFilter);
    }

    // Filtrer par date de fin
    if (dateEndFilter) {
        filteredDepenses = filteredDepenses.filter(depense => depense.date_depense <= dateEndFilter);
    }

    // Afficher les résultats filtrés
    displayDepenses(filteredDepenses);
}

// Afficher le formulaire de création
function showCreateForm() {
    document.getElementById('modal-title').textContent = 'Nouvelle Dépense';
    document.getElementById('depense-form').reset();
    document.getElementById('depense-id').value = '';
    document.getElementById('date_depense').value = new Date().toISOString().split('T')[0];
    document.getElementById('depense-modal').classList.remove('hidden');
}

// Afficher le formulaire d'édition
function editDepense(id) {
    const depense = depenses.find(d => d.id === id);
    if (!depense) return;

    // Remplir le formulaire avec les données de la dépense
    document.getElementById('modal-title').textContent = 'Modifier la Dépense';
    document.getElementById('depense-id').value = depense.id;
    document.getElementById('categorie').value = depense.categorie;
    document.getElementById('montant').value = depense.montant;
    document.getElementById('description').value = depense.description;
    document.getElementById('date_depense').value = depense.date_depense;
    
    document.getElementById('depense-modal').classList.remove('hidden');
}

// Cacher le modal
function hideModal() {
    document.getElementById('depense-modal').classList.add('hidden');
}

// Supprimer une dépense
function deleteDepense(id) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cette dépense ?')) return;

    const token = localStorage.getItem('auth_token');
    if (!token) {
        showError('Token d\'authentification manquant');
        return;
    }

    // Appel API pour supprimer
    fetch(`http://127.0.0.1:8000/api/depenses/${id}`, {
        method: 'DELETE',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadDepenses();
            showSuccess('Dépense supprimée avec succès');
        } else {
            showError(data.message || 'Erreur lors de la suppression');
        }
    })
    .catch(error => {
        showError('Erreur de connexion au serveur');
    });
}

// Gestion de la soumission du formulaire
document.getElementById('depense-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Récupérer les données du formulaire
    const formData = {
        categorie: document.getElementById('categorie').value,
        montant: document.getElementById('montant').value,
        description: document.getElementById('description').value,
        date_depense: document.getElementById('date_depense').value
    };

    const depenseId = document.getElementById('depense-id').value;
    const isEdit = depenseId !== '';

    const token = localStorage.getItem('auth_token');
    if (!token) {
        showError('Token d\'authentification manquant');
        return;
    }

    // Déterminer l'URL et la méthode selon si c'est une création ou modification
    const url = isEdit ? `http://127.0.0.1:8000/api/depenses/${depenseId}` : 'http://127.0.0.1:8000/api/depenses';
    const method = isEdit ? 'PUT' : 'POST';

    // Appel API pour créer ou modifier
    fetch(url, {
        method: method,
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            hideModal();
            loadDepenses();
            showSuccess(isEdit ? 'Dépense modifiée avec succès' : 'Dépense créée avec succès');
        } else {
            showError(data.message || 'Erreur lors de l\'enregistrement');
        }
    })
    .catch(error => {
        showError('Erreur de connexion au serveur');
    });
});

// Déconnexion
function logout() {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user_data');
    window.location.href = '/login';
}

// Afficher un message de succès
function showSuccess(message) {
    alert('Succès: ' + message);
}

// Afficher un message d'erreur
function showError(message) {
    alert('Erreur: ' + message);
} 