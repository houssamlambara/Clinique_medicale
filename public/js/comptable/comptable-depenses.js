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
    if (!userData) return;
    
    currentComptable = JSON.parse(userData);
    if (currentComptable.role !== 'comptable') return;
    
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

    fetch('http://127.0.0.1:8000/api/depenses', {
        method: 'GET',
        headers: getHeaders(token)
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

    container.innerHTML = '';

    if (depensesToDisplay.length === 0) {
        noDepenses.classList.remove('hidden');
        return;
    }

    noDepenses.classList.add('hidden');

    depensesToDisplay.forEach(depense => {
        const depenseCard = createDepenseCard(depense);
        container.appendChild(depenseCard);
    });
}

// Créer une carte HTML pour une dépense
function createDepenseCard(depense) {
    const div = document.createElement('div');
    div.className = 'bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow';
    
    const isPayee = depense.est_paye;
    const statusClass = isPayee ? 'border-green-300 bg-green-50' : 'border-red-300 bg-red-50';
    
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
                <select onchange="changerStatutDepense(${depense.id}, this.value)" class="px-4 py-2 rounded-lg text-sm border-2 border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white shadow-sm hover:border-gray-300 ${statusClass}">
                    <option value="false" ${!isPayee ? 'selected' : ''} class="text-red-700">Non payée</option>
                    <option value="true" ${isPayee ? 'selected' : ''} class="text-green-700">Payée</option>
                </select>
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
    const statusFilter = document.getElementById('filter-status').value;
    const categorieFilter = document.getElementById('filter-categorie').value;
    const dateStartFilter = document.getElementById('filter-date-start').value;
    const dateEndFilter = document.getElementById('filter-date-end').value;

    let filteredDepenses = depenses;

    if (statusFilter) {
        filteredDepenses = filteredDepenses.filter(depense => {
            if (statusFilter === 'paye') return depense.est_paye;
            if (statusFilter === 'non-paye') return !depense.est_paye;
            return true;
        });
    }

    if (categorieFilter) {
        filteredDepenses = filteredDepenses.filter(depense => depense.categorie === categorieFilter);
    }

    if (dateStartFilter) {
        filteredDepenses = filteredDepenses.filter(depense => depense.date_depense >= dateStartFilter);
    }

    if (dateEndFilter) {
        filteredDepenses = filteredDepenses.filter(depense => depense.date_depense <= dateEndFilter);
    }

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

// Changer le statut d'une dépense via le select
function changerStatutDepense(id, newStatus) {
    const token = localStorage.getItem('auth_token');
    if (!token) {
        showError('Token d\'authentification manquant');
        return;
    }

    const isPayee = newStatus === 'true';
    const formData = {
        est_paye: isPayee,
        date_paiement: isPayee ? new Date().toISOString().split('T')[0] : null
    };

    fetch(`http://127.0.0.1:8000/api/depenses/${id}`, {
        method: 'PUT',
        headers: getHeaders(token),
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadDepenses();
            showSuccess(isPayee ? 'Dépense marquée comme payée' : 'Dépense marquée comme non payée');
        } else {
            showError(data.message || 'Erreur lors de la mise à jour');
        }
    })
    .catch(error => {
        showError('Erreur de connexion au serveur');
    });
}

// Supprimer une dépense
function deleteDepense(id) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cette dépense ?')) return;

    const token = localStorage.getItem('auth_token');
    if (!token) {
        showError('Token d\'authentification manquant');
        return;
    }

    fetch(`http://127.0.0.1:8000/api/depenses/${id}`, {
        method: 'DELETE',
        headers: getHeaders(token)
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

// Obtenir les headers pour les requêtes API
function getHeaders(token) {
    return {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    };
}

// Afficher un message de succès
function showSuccess(message) {
    alert('Succès: ' + message);
}

// Afficher un message d'erreur
function showError(message) {
    alert('Erreur: ' + message);
}

// Déconnexion
function logout() {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user_data');
    window.location.href = '/login';
}

// Gestion de la soumission du formulaire
document.getElementById('depense-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
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

    const url = isEdit ? `http://127.0.0.1:8000/api/depenses/${depenseId}` : 'http://127.0.0.1:8000/api/depenses';
    const method = isEdit ? 'PUT' : 'POST';

    fetch(url, {
        method: method,
        headers: getHeaders(token),
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