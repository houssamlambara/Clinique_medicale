<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mes Prescriptions - Clinique Médicale</title>
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
                        <a href="/patient/dashboard" class="text-gray-600 hover:text-gray-900">
                            <i class="fas fa-arrow-left text-xl"></i>
                        </a>
                        <h1 class="text-2xl font-bold text-gray-900">Mes Prescriptions</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="/patient/dashboard" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-arrow-left mr-2"></i>Retour
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Error Message -->
            <div id="error-message" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                <span id="error-text"></span>
            </div>

            <!-- Success Message -->
            <div id="success-message" class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                <span id="success-text"></span>
            </div>

            <!-- Loading -->
            <div id="loading" class="flex justify-center items-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
            </div>

            <!-- No Prescriptions Message -->
            <div id="no-prescriptions" class="hidden text-center py-12">
                <div class="bg-white rounded-xl p-8 shadow-sm">
                    <i class="fas fa-pills text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Aucune prescription</h3>
                    <p class="text-gray-600">Vous n'avez pas encore de prescriptions médicales.</p>
                </div>
            </div>

            <!-- Prescriptions List -->
            <div id="prescriptions-list" class="space-y-6"></div>
        </main>
    </div>

    <script src="/js/patient/patient-prescriptions.js"></script>
</body>
</html> 