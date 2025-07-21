<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestion des Factures - Clinique Médicale</title>
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
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-3 rounded-xl">
                            <i class="fas fa-file-invoice text-white text-2xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Gestion des Factures</h1>
                            <p class="text-sm text-gray-600">Créer et gérer les factures des patients</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button onclick="showCreateForm()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-plus mr-2"></i>Nouvelle Facture
                        </button>
                        <a href="/comptable/dashboard" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-arrow-left mr-2"></i>Retour
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            <!-- Filtres -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                        <select id="filter-status" onchange="filterFactures()" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Tous</option>
                            <option value="paye">Payées</option>
                            <option value="non-paye">Non payées</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Patient</label>
                        <select id="filter-patient" onchange="filterFactures()" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Tous les patients</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Liste des factures -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Factures</h3>
                </div>
                <div class="p-6">
                    <div id="no-factures" class="text-center py-8">
                        <i class="fas fa-file-invoice text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-600">Aucune facture trouvée</p>
                    </div>
                    <div id="factures-list" class="space-y-4"></div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Création/Édition -->
    <div id="facture-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900" id="modal-title">Nouvelle Facture</h3>
                </div>
                <form id="facture-form" class="p-6 space-y-4">
                    <input type="hidden" id="facture-id">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Patient</label>
                        <select id="patient-id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Sélectionner un patient</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Consultation</label>
                        <select id="consultation-id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Sélectionner une consultation</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Montant (€)</label>
                        <input type="number" id="montant" step="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="hideModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                            Annuler
                        </button>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="/js/comptable/comptable-factures.js"></script>
</body>

</html>