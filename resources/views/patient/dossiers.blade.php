<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mes Dossiers Médicaux</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="{{ asset('js/patient/patient-dossiers.js') }}"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-lg border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-3 rounded-xl shadow-lg">
                        <i class="fas fa-folder text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Mes Dossiers Médicaux</h1>
                        <p class="text-sm text-gray-600">Consulter mes dossiers médicaux</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="/patient/dashboard" class="bg-gradient-to-r from-gray-500 to-gray-600 text-white px-6 py-2 rounded-lg hover:from-gray-600 hover:to-gray-700 transition-all duration-200 transform hover:scale-105 shadow-md">
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
                <i class="fas fa-folder text-gray-400 text-4xl mb-4"></i>
                <p class="text-gray-500 font-medium">Aucun dossier médical trouvé</p>
                <p class="text-sm text-gray-400 mt-2">Vous n'avez pas encore de dossiers médicaux</p>
            </div>

            <!-- Message d'erreur -->
            <div id="error-message" class="hidden text-center py-12">
                <i class="fas fa-exclamation-triangle text-red-400 text-4xl mb-4"></i>
                <p class="text-red-500 font-medium" id="error-text">Erreur de chargement</p>
            </div>
        </div>
    </main>

    <!-- Message de succès -->
    <div id="successMessage" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 hidden">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <span>Action effectuée avec succès !</span>
        </div>
    </div>
</body>
</html> 