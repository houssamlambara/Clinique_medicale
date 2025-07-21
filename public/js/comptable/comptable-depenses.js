let currentComptable = null;
let depenses = [];

document.addEventListener('DOMContentLoaded', function () {
    loadUserData();
    loadDepenses();
});

function loadUserData() {
    const userData = localStorage.getItem('user_data');
    
    if (userData) {
        currentComptable = JSON.parse(userData);
        
        if (currentComptable.role !== 'comptable') {
            showError('Accès non autorisé');
            return;
        }
        
        // Vérifier si l'élément existe avant de le modifier
        const comptableNameElement = document.getElementById('comptable-name');
        if (comptableNameElement) {
            comptableNameElement.textContent = `${currentComptable.prenom} ${currentComptable.nom}`;
        }
    } else {
        showError('Aucun utilisateur connecté');
    }
}

function loadDepenses() {
    const token = localStorage.getItem('auth_token');
    if (!token) {
        showError('Token d\'authentification manquant');
        return;
    }

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
        const div = document.createElement('div');
        div.className = 'bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow';
        
        const statusClass = depense.est_paye ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
        const statusText = depense.est_paye ? 'Payée' : 'Non payée';
        
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
                    <p class="text-lg font-bold text-red-600">${parseFloat(depense.montant).toLocaleString()} €</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-700">Catégorie</p>
                    <p class="text-sm text-gray-600">${depense.categorie}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-700">Date de dépense</p>
                    <p class="text-sm text-gray-600">${new Date(depense.date_depense).toLocaleDateString('fr-FR')}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-700">Date de paiement</p>
                    <p class="text-sm text-gray-600">${depense.date_paiement ? new Date(depense.date_paiement).toLocaleDateString('fr-FR') : 'Non payée'}</p>
                </div>
            </div>
        `;
        
        container.appendChild(div);
    });
}

function filterDepenses() {
    const statusFilter = document.getElementById('filter-status').value;
    const categorieFilter = document.getElementById('filter-categorie').value;
    const dateStartFilter = document.getElementById('filter-date-start').value;
    const dateEndFilter = document.getElementById('filter-date-end').value;

    let filteredDepenses = depenses;

    if (statusFilter) {
        filteredDepenses = filteredDepenses.filter(d => {
            if (statusFilter === 'paye') return d.est_paye;
            if (statusFilter === 'non-paye') return !d.est_paye;
            return true;
        });
    }

    if (categorieFilter) {
        filteredDepenses = filteredDepenses.filter(d => d.categorie === categorieFilter);
    }

    if (dateStartFilter) {
        filteredDepenses = filteredDepenses.filter(d => d.date_depense >= dateStartFilter);
    }

    if (dateEndFilter) {
        filteredDepenses = filteredDepenses.filter(d => d.date_depense <= dateEndFilter);
    }

    displayDepenses(filteredDepenses);
}

function showCreateForm() {
    document.getElementById('modal-title').textContent = 'Nouvelle Dépense';
    document.getElementById('depense-form').reset();
    document.getElementById('depense-id').value = '';
    document.getElementById('date_depense').value = new Date().toISOString().split('T')[0];
    document.getElementById('depense-modal').classList.remove('hidden');
}

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

function hideModal() {
    document.getElementById('depense-modal').classList.add('hidden');
}

function deleteDepense(id) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cette dépense ?')) return;

    const token = localStorage.getItem('auth_token');
    if (!token) {
        showError('Token d\'authentification manquant');
        return;
    }

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

function logout() {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user_data');
    window.location.href = '/login';
}

function showSuccess(message) {
    alert('Succès: ' + message);
}

function showError(message) {
    alert('Erreur: ' + message);
} 