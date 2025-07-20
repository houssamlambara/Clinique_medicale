<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dossiers Médicaux</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="{{ asset('js/medecin/medecin-dossiers.js') }}"></script>
</head>

<body class="bg-gradient-to-br from-teal-50 to-cyan-100 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-lg border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <div class="bg-gradient-to-r from-teal-500 to-cyan-600 p-3 rounded-xl shadow-lg">
                        <i class="fas fa-folder-medical text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Dossiers Médicaux</h1>
                        <p class="text-sm text-gray-600">Gérer les dossiers de mes patients</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <button onclick="openCreateDossierModal()" class="bg-gradient-to-r from-teal-500 to-cyan-600 text-white px-6 py-3 rounded-lg hover:from-teal-600 hover:to-cyan-700 transition-all duration-200 transform hover:scale-105 shadow-md">
                        <i class="fas fa-plus mr-2"></i>Ajouter un dossier
                    </button>
                    <a href="/medecin/dashboard" class="bg-gradient-to-r from-gray-500 to-gray-600 text-white px-6 py-2 rounded-lg hover:from-gray-600 hover:to-gray-700 transition-all duration-200 transform hover:scale-105 shadow-md">
                        <i class="fas fa-arrow-left mr-2"></i>Retour
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <!-- Liste des dossiers -->
        <div class="bg-white rounded-2xl shadow-lg p-6">

            <!-- Liste -->
            <div id="dossiers-list" class="space-y-4">
                <!-- Les dossiers seront chargés ici -->
            </div>

            <!-- Message si aucun dossier -->
            <div id="no-dossiers" class="hidden text-center py-12">
                <i class="fas fa-folder-medical text-gray-400 text-4xl mb-4"></i>
                <p class="text-gray-500 font-medium">Aucun dossier médical trouvé</p>
                <p class="text-sm text-gray-400 mt-2">Vous n'avez pas encore de dossiers médicaux pour vos patients</p>
            </div>

            <!-- Message d'erreur -->
            <div id="error-message" class="hidden text-center py-12">
                <i class="fas fa-exclamation-triangle text-red-400 text-4xl mb-4"></i>
                <p class="text-red-500 font-medium" id="error-text">Erreur de chargement</p>
            </div>
        </div>
    </main>



    <!-- Modal pour créer un nouveau dossier -->
    <div id="createDossierModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <!-- Header du modal -->
                <div class="flex items-center justify-between p-6 border-b border-gray-200">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Créer un Dossier Médical</h2>
                        <p class="text-gray-600 mt-1">Créer un nouveau dossier pour un patient</p>
                    </div>
                    <button onclick="closeCreateDossierModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>

                <!-- Contenu du modal -->
                <div class="p-6">
                    <form id="createDossierForm" class="space-y-6">
                        <!-- Sélection du patient -->
                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-2 rounded-lg mr-3">
                                    <i class="fas fa-user text-white text-sm"></i>
                                </div>
                                Patient
                            </label>
                            <select id="createPatientSelect" class="w-full p-4 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 bg-gray-50 hover:bg-white">
                                <option value="">Sélectionnez un patient...</option>
                            </select>
                        </div>
                        
                        <!-- Notes -->
                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                <div class="bg-gradient-to-r from-teal-500 to-teal-600 p-2 rounded-lg mr-3">
                                    <i class="fas fa-notes-medical text-white text-sm"></i>
                                </div>
                                Notes du dossier
                            </label>
                            <textarea id="createNoteInput" rows="8" 
                                      class="w-full p-4 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 bg-gray-50 hover:bg-white resize-none" 
                                      placeholder="Ajoutez vos observations médicales..."></textarea>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                            <button type="button" onclick="closeCreateDossierModal()" 
                                    class="px-8 py-3 border-2 border-gray-300 rounded-xl text-gray-700 font-semibold hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 transform hover:scale-105">
                                <i class="fas fa-times mr-2"></i>Annuler
                            </button>
                            <button type="button" onclick="createDossier()" 
                                    class="px-8 py-3 bg-gradient-to-r from-teal-500 to-cyan-600 text-white rounded-xl font-semibold hover:from-teal-600 hover:to-cyan-700 transition-all duration-200 transform hover:scale-105 shadow-lg">
                                <i class="fas fa-save mr-2"></i>Créer le dossier
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour modifier un dossier -->
    <div id="editDossierModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <!-- Header du modal -->
                <div class="flex items-center justify-between p-6 border-b border-gray-200">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Modifier le Dossier</h2>
                        <p class="text-gray-600 mt-1">Modifier les notes du dossier médical</p>
                    </div>
                    <button onclick="closeEditDossierModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>

                <!-- Contenu du modal -->
                <div class="p-6">
                    <form id="editDossierForm" class="space-y-6">
                        <input type="hidden" id="editDossierId">

                        <!-- Notes -->
                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                <div class="bg-gradient-to-r from-teal-500 to-teal-600 p-2 rounded-lg mr-3">
                                    <i class="fas fa-notes-medical text-white text-sm"></i>
                                </div>
                                Notes du dossier
                            </label>
                            <textarea id="editNoteInput" rows="8"
                                class="w-full p-4 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 bg-gray-50 hover:bg-white resize-none"
                                placeholder="Ajoutez vos observations médicales..."></textarea>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                            <button type="button" onclick="closeEditDossierModal()"
                                class="px-8 py-3 border-2 border-gray-300 rounded-xl text-gray-700 font-semibold hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 transform hover:scale-105">
                                <i class="fas fa-times mr-2"></i>Annuler
                            </button>
                            <button type="button" onclick="updateDossier()"
                                class="px-8 py-3 bg-gradient-to-r from-teal-500 to-cyan-600 text-white rounded-xl font-semibold hover:from-teal-600 hover:to-cyan-700 transition-all duration-200 transform hover:scale-105 shadow-lg">
                                <i class="fas fa-save mr-2"></i>Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Message de succès -->
    <div id="successMessage" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 hidden">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <span>Action effectuée avec succès !</span>
        </div>
    </div>
</body>

</html>