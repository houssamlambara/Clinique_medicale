<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestion des Dépenses - Clinique Médicale</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center space-x-4">
                        <div class="bg-gradient-to-r from-red-500 to-red-600 p-3 rounded-xl">
                            <i class="fas fa-money-bill-wave text-white text-2xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Gestion des Dépenses</h1>
                            <p class="text-sm text-gray-600">Suivre les dépenses de la clinique</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="/comptable/dashboard" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-arrow-left mr-2"></i>Retour
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Actions -->
            <div class="mb-6 flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-900">Liste des Dépenses</h2>
                <button onclick="showCreateForm()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                    <i class="fas fa-plus mr-2"></i>Nouvelle Dépense
                </button>
            </div>

            <!-- Filtres -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                        <select id="filter-status" onchange="filterDepenses()" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            <option value="">Toutes</option>
                            <option value="paye">Payées</option>
                            <option value="non-paye">Non payées</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catégorie</label>
                        <select id="filter-categorie" onchange="filterDepenses()" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            <option value="">Toutes les catégories</option>
                            <option value="fournitures">Fournitures médicales</option>
                            <option value="equipement">Équipement</option>
                            <option value="personnel">Personnel</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="autres">Autres</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Liste des dépenses -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Dépenses</h3>
                </div>
                <div class="p-6">
                    <div id="no-depenses" class="text-center py-8">
                        <i class="fas fa-money-bill-wave text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-600">Aucune dépense trouvée</p>
                    </div>
                    <div id="depenses-list" class="space-y-4"></div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Création/Édition -->
    <div id="depense-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900" id="modal-title">Nouvelle Dépense</h3>
                </div>
                <form id="depense-form" class="p-6 space-y-4">
                    <input type="hidden" id="depense-id">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catégorie</label>
                        <select id="categorie" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            <option value="">Sélectionner une catégorie</option>
                            <option value="fournitures">Fournitures médicales</option>
                            <option value="equipement">Équipement</option>
                            <option value="personnel">Personnel</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="autres">Autres</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Montant (€)</label>
                        <input type="number" id="montant" step="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea id="description" rows="3" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date de dépense</label>
                        <input type="date" id="date_depense" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="hideModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                            Annuler
                        </button>
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="/js/comptable/comptable-depenses.js"></script>
</body>

</html>