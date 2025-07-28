<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestion des Notifications</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="{{ asset('js/secretaire/secretaire-notifications.js') }}"></script>
</head>

<body class="bg-gradient-to-br from-orange-50 to-red-100 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-lg border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <div class="bg-gradient-to-r from-orange-500 to-red-600 p-3 rounded-xl shadow-lg">
                        <i class="fas fa-bell text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Gestion des Notifications</h1>
                        <p class="text-sm text-gray-600">Envoyer des notifications aux patients</p>
                    </div>
                </div>
                <!-- Boutons d'action -->
                <div class="mb-6 flex flex-wrap gap-4">
                    <button onclick="openSendNotificationModal()" class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-3 rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 transform hover:scale-105 shadow-md">
                        <i class="fas fa-plus mr-2"></i>Nouvelle Notification
                    </button>

                    <div class="flex items-center space-x-4">
                        <a href="/secretaire/dashboard" class="bg-gradient-to-r from-gray-500 to-gray-600 text-white px-6 py-2 rounded-lg hover:from-gray-600 hover:to-gray-700 transition-all duration-200 transform hover:scale-105 shadow-md">
                            <i class="fas fa-arrow-left mr-2"></i>Retour
                        </a>
                    </div>
                </div>
            </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

        <!-- Liste des notifications -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Notifications Envoyées</h2>

            <!-- Liste -->
            <div id="notifications-list" class="space-y-4">
                <!-- Les notifications seront chargées ici -->
            </div>

            <!-- Message si aucune notification -->
            <div id="no-notifications" class="hidden text-center py-12">
                <i class="fas fa-bell text-gray-400 text-4xl mb-4"></i>
                <p class="text-gray-500 font-medium">Aucune notification envoyée</p>
                <p class="text-sm text-gray-400 mt-2">Commencez par envoyer votre première notification</p>
            </div>

            <!-- Message d'erreur -->
            <div id="error-message" class="hidden text-center py-12">
                <i class="fas fa-exclamation-triangle text-red-400 text-4xl mb-4"></i>
                <p class="text-red-500 font-medium" id="error-text">Erreur de chargement</p>
            </div>
        </div>
    </main>

    <!-- Modal Nouvelle Notification -->
    <div id="sendNotificationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Nouvelle Notification</h3>
                <button onclick="closeSendNotificationModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form id="sendNotificationForm">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Patient</label>
                    <select id="notificationPatient" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Sélectionner un patient</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <select id="notificationType" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Sélectionner un type</option>
                        <option value="rendezvous">Rendez-vous</option>
                        <option value="consultation">Consultation</option>
                        <option value="resultat">Résultat</option>
                        <option value="information">Information</option>
                        <option value="rappel">Rappel</option>
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                    <textarea id="notificationMessage" rows="4" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Entrez votre message..." required></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeSendNotificationModal()" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                        Envoyer
                    </button>
                </div>
            </form>
        </div>
    </div>



    <!-- Messages de succès/erreur -->
    <div id="successMessage" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 hidden">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <span id="successText">Action effectuée avec succès !</span>
        </div>
    </div>

    <div id="errorMessage" class="fixed top-4 right-4 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 hidden">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <span id="errorText">Erreur lors de l'action</span>
        </div>
    </div>
</body>

</html>