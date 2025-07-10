<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Médecin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <i class="fas fa-user-md text-green-600 text-2xl mr-3"></i>
                    <h1 class="text-2xl font-bold text-gray-900">Dashboard Médecin</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">Dr. {{ Auth::user()->prenom }} {{ Auth::user()->nom }}</span>
                    <form method="POST" action="/logout" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
                            <i class="fas fa-sign-out-alt mr-2"></i>Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Rendez-vous Aujourd'hui -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-calendar-day text-blue-600 text-3xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">RDV Aujourd'hui</dt>
                                    <dd class="text-lg font-medium text-gray-900">0</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Patients en Attente -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-users text-orange-600 text-3xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Patients en Attente</dt>
                                    <dd class="text-lg font-medium text-gray-900">0</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Consultations du Jour -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-stethoscope text-green-600 text-3xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Consultations</dt>
                                    <dd class="text-lg font-medium text-gray-900">0</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ordonnances à Rédiger -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-prescription text-purple-600 text-3xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Ordonnances</dt>
                                    <dd class="text-lg font-medium text-gray-900">0</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions Rapides -->
            <div class="mt-8">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Actions Rapides</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <button class="bg-blue-500 text-white p-4 rounded-lg hover:bg-blue-600 transition duration-200">
                        <i class="fas fa-calendar-plus text-2xl mb-2"></i>
                        <div>Mon Planning</div>
                    </button>
                    
                    <button class="bg-green-500 text-white p-4 rounded-lg hover:bg-green-600 transition duration-200">
                        <i class="fas fa-user-injured text-2xl mb-2"></i>
                        <div>Mes Patients</div>
                    </button>
                    
                    <button class="bg-purple-500 text-white p-4 rounded-lg hover:bg-purple-600 transition duration-200">
                        <i class="fas fa-file-medical text-2xl mb-2"></i>
                        <div>Rédiger Ordonnance</div>
                    </button>
                    
                    <button class="bg-orange-500 text-white p-4 rounded-lg hover:bg-orange-600 transition duration-200">
                        <i class="fas fa-chart-line text-2xl mb-2"></i>
                        <div>Mes Rapports</div>
                    </button>
                </div>
            </div>

            <!-- Prochains Rendez-vous -->
            <div class="mt-8">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Prochains Rendez-vous</h2>
                <div class="bg-white shadow overflow-hidden sm:rounded-md">
                    <ul class="divide-y divide-gray-200">
                        <li class="px-6 py-4">
                            <div class="text-center text-gray-500">
                                Aucun rendez-vous programmé
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </main>
</body>
</html> 