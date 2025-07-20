<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestion des Rendez-vous</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="{{ asset('js/patient/rendezvous-patient.js') }}"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-lg border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-3 rounded-xl shadow-lg">
                        <i class="fas fa-calendar-check text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Mes Rendez-vous</h1>
                        <p class="text-sm text-gray-600">Vos rendez-vous médicaux</p>
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
        <!-- Liste des rendez-vous -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900">Mes Rendez-vous</h2>
                <div class="flex items-center space-x-2">
                    <button onclick="openAppointmentModal()" class="bg-gradient-to-r from-green-500 to-green-600 text-white px-4 py-2 rounded-lg hover:from-green-600 hover:to-green-700 transition-all duration-200 transform hover:scale-105 shadow-md text-sm">
                        <i class="fas fa-plus mr-2"></i>Nouveau Rendez-vous
                    </button>
                </div>
            </div>

            <!-- Liste -->
            <div id="rendezvous-list" class="space-y-4">
                <!-- Les rendez-vous seront chargés ici -->
            </div>

            <!-- Message si aucun rendez-vous -->
            <div id="no-rendezvous" class="hidden text-center py-12">
                <i class="fas fa-calendar-times text-gray-400 text-4xl mb-4"></i>
                <p class="text-gray-500 font-medium">Aucun rendez-vous trouvé</p>
                <p class="text-sm text-gray-400 mt-2">Vous n'avez pas encore de rendez-vous</p>
            </div>

            <!-- Message d'erreur -->
            <div id="error-message" class="hidden text-center py-12">
                <i class="fas fa-exclamation-triangle text-red-400 text-4xl mb-4"></i>
                <p class="text-red-500 font-medium" id="error-text">Erreur de chargement</p>
            </div>
        </div>
    </main>

    <!-- Modal pour prendre un rendez-vous -->
    <div id="appointmentModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                <!-- Header du modal -->
                <div class="flex items-center justify-between p-6 border-b border-gray-200">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Prendre un Rendez-vous</h2>
                        <p class="text-gray-600 mt-1">Sélectionnez une date et un créneau horaire</p>
                    </div>
                    <button onclick="closeAppointmentModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>

                <!-- Contenu du modal -->
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Calendrier -->
                        <div class="bg-gray-50 rounded-xl p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                                <i class="fas fa-calendar mr-2"></i>Sélectionner une date
                            </h3>
                            
                            <!-- Navigation du mois -->
                            <div class="flex items-center justify-between mb-4">
                                <button id="prevMonth" class="p-2 hover:bg-gray-200 rounded-lg transition duration-200">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <h4 id="currentMonth" class="text-lg font-semibold text-gray-800"></h4>
                                <button id="nextMonth" class="p-2 hover:bg-gray-200 rounded-lg transition duration-200">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>

                            <!-- Jours de la semaine -->
                            <div class="grid grid-cols-7 gap-1 mb-2">
                                <div class="text-center text-sm font-medium text-gray-500 py-2">Dim</div>
                                <div class="text-center text-sm font-medium text-gray-500 py-2">Lun</div>
                                <div class="text-center text-sm font-medium text-gray-500 py-2">Mar</div>
                                <div class="text-center text-sm font-medium text-gray-500 py-2">Mer</div>
                                <div class="text-center text-sm font-medium text-gray-500 py-2">Jeu</div>
                                <div class="text-center text-sm font-medium text-gray-500 py-2">Ven</div>
                                <div class="text-center text-sm font-medium text-gray-500 py-2">Sam</div>
                            </div>

                            <!-- Grille du calendrier -->
                            <div id="calendarGrid" class="grid grid-cols-7 gap-1">
                                <!-- Les jours seront générés par JavaScript -->
                            </div>
                        </div>
                        
                        <!-- Créneaux horaires -->
                        <div class="bg-gray-50 rounded-xl p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                                <i class="fas fa-clock mr-2"></i>Sélectionner un créneau
                            </h3>
                            
                            <div id="selectedDate" class="text-sm text-gray-600 mb-4">
                                Veuillez sélectionner une date
                            </div>

                            <!-- Sélection du médecin -->
                            <div class="mb-6">
                                <h4 class="text-md font-medium text-gray-700 mb-3">
                                    <i class="fas fa-user-md mr-2"></i>Choisir un médecin
                                </h4>
                                <select id="doctorSelect" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Sélectionnez un médecin</option>
                                    <!-- Les médecins seront chargés par JavaScript -->
                                </select>
                            </div>

                            <!-- Créneaux du matin -->
                            <div class="mb-6">
                                <h4 class="text-md font-medium text-gray-700 mb-3">Matin</h4>
                                <div class="grid grid-cols-3 gap-2">
                                    <div class="time-slot p-3 text-center border border-gray-300 rounded-lg hover:bg-blue-50 cursor-pointer" data-time="09:00">09:00</div>
                                    <div class="time-slot p-3 text-center border border-gray-300 rounded-lg hover:bg-blue-50 cursor-pointer" data-time="09:30">09:30</div>
                                    <div class="time-slot p-3 text-center border border-gray-300 rounded-lg hover:bg-blue-50 cursor-pointer" data-time="10:00">10:00</div>
                                    <div class="time-slot p-3 text-center border border-gray-300 rounded-lg hover:bg-blue-50 cursor-pointer" data-time="10:30">10:30</div>
                                    <div class="time-slot p-3 text-center border border-gray-300 rounded-lg hover:bg-blue-50 cursor-pointer" data-time="11:00">11:00</div>
                                    <div class="time-slot p-3 text-center border border-gray-300 rounded-lg hover:bg-blue-50 cursor-pointer" data-time="11:30">11:30</div>
                                </div>
                            </div>

                            <!-- Créneaux de l'après-midi -->
                            <div class="mb-6">
                                <h4 class="text-md font-medium text-gray-700 mb-3">Après-midi</h4>
                                <div class="grid grid-cols-3 gap-2">
                                    <div class="time-slot p-3 text-center border border-gray-300 rounded-lg hover:bg-blue-50 cursor-pointer" data-time="14:00">14:00</div>
                                    <div class="time-slot p-3 text-center border border-gray-300 rounded-lg hover:bg-blue-50 cursor-pointer" data-time="14:30">14:30</div>
                                    <div class="time-slot p-3 text-center border border-gray-300 rounded-lg hover:bg-blue-50 cursor-pointer" data-time="15:00">15:00</div>
                                    <div class="time-slot p-3 text-center border border-gray-300 rounded-lg hover:bg-blue-50 cursor-pointer" data-time="15:30">15:30</div>
                                    <div class="time-slot p-3 text-center border border-gray-300 rounded-lg hover:bg-blue-50 cursor-pointer" data-time="16:00">16:00</div>
                                    <div class="time-slot p-3 text-center border border-gray-300 rounded-lg hover:bg-blue-50 cursor-pointer" data-time="16:30">16:30</div>
                                </div>
                        </div>
                        
                            <!-- Résumé du rendez-vous -->
                            <div id="appointmentSummary" class="bg-white rounded-lg p-4 mb-4 hidden">
                                <h4 class="text-md font-semibold text-gray-800 mb-3">
                                    <i class="fas fa-calendar-check mr-2"></i>Résumé
                                </h4>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Date:</span>
                                        <span id="summaryDate" class="text-sm font-medium text-gray-800"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Heure:</span>
                                        <span id="summaryTime" class="text-sm font-medium text-gray-800"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Médecin:</span>
                                        <span id="summaryDoctor" class="text-sm font-medium text-gray-800"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Bouton de confirmation -->
                            <button id="confirmButton" disabled
                                class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white py-3 px-4 rounded-lg font-semibold hover:from-green-600 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transform hover:scale-[1.02] transition duration-200 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fas fa-check mr-2"></i>Confirmer le rendez-vous
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour modifier un rendez-vous -->
    <div id="editAppointmentModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto border border-gray-100">
                <!-- Header du modal avec gradient -->
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-t-3xl p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="bg-white bg-opacity-20 p-3 rounded-xl">
                                <i class="fas fa-edit text-white text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-white">Modifier le Rendez-vous</h2>
                                <p class="text-blue-100 mt-1">Modifier les détails du rendez-vous</p>
                            </div>
                        </div>
                        <button onclick="closeEditAppointmentModal()" class="text-white hover:text-blue-100 transition-colors p-2 rounded-full hover:bg-white hover:bg-opacity-20">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Contenu du modal -->
                <div class="p-8">
                    <form id="editAppointmentForm" class="space-y-6">
                        <input type="hidden" id="editRendezVousId">
                        
                        <!-- Date -->
                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-2 rounded-lg mr-3">
                                    <i class="fas fa-calendar text-white text-sm"></i>
                                </div>
                                Date du rendez-vous
                            </label>
                            <input type="date" id="editDateInput" 
                                   class="w-full p-4 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-gray-50 hover:bg-white" 
                                   required>
                        </div>

                        <!-- Heure -->
                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                <div class="bg-gradient-to-r from-green-500 to-green-600 p-2 rounded-lg mr-3">
                                    <i class="fas fa-clock text-white text-sm"></i>
                                </div>
                                Heure du rendez-vous
                            </label>
                            <input type="time" id="editTimeInput" 
                                   class="w-full p-4 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 bg-gray-50 hover:bg-white" 
                                   required>
                        </div>

                        <!-- Médecin -->
                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                <div class="bg-gradient-to-r from-purple-500 to-purple-600 p-2 rounded-lg mr-3">
                                    <i class="fas fa-user-md text-white text-sm"></i>
                                </div>
                                Médecin
                            </label>
                            <select id="editDoctorSelect" 
                                    class="w-full p-4 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-gray-50 hover:bg-white" 
                                    required>
                                <option value="">Sélectionnez un médecin</option>
                                <!-- Les médecins seront chargés par JavaScript -->
                            </select>
                        </div>

                        <!-- Raison -->
                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                <div class="bg-gradient-to-r from-orange-500 to-orange-600 p-2 rounded-lg mr-3">
                                    <i class="fas fa-notes-medical text-white text-sm"></i>
                                </div>
                                Raison du rendez-vous
                            </label>
                            <textarea id="editRaisonInput" rows="4" 
                                      class="w-full p-4 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 bg-gray-50 hover:bg-white resize-none" 
                                      placeholder="Décrivez la raison de votre rendez-vous..."></textarea>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                            <button type="button" onclick="closeEditAppointmentModal()" 
                                    class="px-8 py-3 border-2 border-gray-300 rounded-xl text-gray-700 font-semibold hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 transform hover:scale-105">
                                <i class="fas fa-times mr-2"></i>Annuler
                            </button>
                            <button type="button" onclick="updateRendezVous()" 
                                    class="px-8 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl font-semibold hover:from-blue-600 hover:to-indigo-700 transition-all duration-200 transform hover:scale-105 shadow-lg">
                                <i class="fas fa-save mr-2"></i>Modifier
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
            <span>Rendez-vous confirmé avec succès !</span>
        </div>
    </div>
</body>
</html> 