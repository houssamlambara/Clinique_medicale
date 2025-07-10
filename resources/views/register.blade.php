<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl p-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="mx-auto w-16 h-16 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-user-plus text-white text-2xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-800 mb-2">Créer un compte</h2>
            <p class="text-gray-600">Rejoignez-nous dès aujourd'hui</p>
        </div>

        <!-- Form -->
        <form method="POST" action="/register" class="space-y-6">
            @csrf
            
            <!-- Informations de base pour tous -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nom</label>
                    <input type="text" name="nom" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Prénom</label>
                    <input type="text" name="prenom" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mot de passe</label>
                    <input type="password" name="password"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           required>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Téléphone</label>
                    <input type="tel" name="telephone" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Je suis un :</label>
                    <select name="role" id="roleSelect"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required onchange="toggleRoleFields()">
                        <option value="">Choisir un rôle</option>
                        <option value="patient">Patient</option>
                        <option value="medecin">Médecin</option>
                        <option value="secretaire">Secrétaire</option>
                        <option value="comptable">Comptable</option>
                    </select>
                </div>
            </div>
            
            <!-- Champs spécifiques aux patients -->
            <div id="patientFields" class="grid grid-cols-1 md:grid-cols-2 gap-6" style="display: none;">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Date de naissance <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="date_naissance" id="dateNaissance"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Genre <span class="text-red-500">*</span>
                    </label>
                    <select name="genre" id="genre"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Choisir</option>
                        <option value="Homme">Homme</option>
                        <option value="Femme">Femme</option>
                    </select>
                </div>
            </div>
            
            <!-- Champs spécifiques aux médecins -->
            <div id="medecinFields" class="grid grid-cols-1 md:grid-cols-2 gap-6" style="display: none;">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Spécialité <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="specialite" id="specialite"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Ex: Cardiologie, Pédiatrie...">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Numéro de licence <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="numero_licence" id="numeroLicence"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Numéro de licence médicale">
                </div>
            </div>
            
            <button type="submit"
                    class="w-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white py-3 px-4 rounded-lg font-semibold hover:from-blue-600 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transform hover:scale-[1.02] transition duration-200 shadow-lg">
                <i class="fas fa-user-plus mr-2"></i>
                S'inscrire
            </button>
        </form>

        <!-- Login Link -->
        <div class="mt-8 text-center">
            <p class="text-sm text-gray-600">
                Vous avez déjà un compte ?
                <a href="/login" class="text-blue-600 hover:text-blue-500 font-medium ml-1">
                    Se connecter
                </a>
            </p>
        </div>
    </div>

    <!-- Background decoration -->
    <div class="fixed top-0 left-0 w-full h-full pointer-events-none overflow-hidden -z-10">
        <div class="absolute top-10 left-10 w-20 h-20 bg-blue-200 rounded-full opacity-50 animate-pulse"></div>
        <div class="absolute top-40 right-20 w-16 h-16 bg-indigo-200 rounded-full opacity-50 animate-pulse delay-1000"></div>
        <div class="absolute bottom-20 left-1/4 w-12 h-12 bg-purple-200 rounded-full opacity-50 animate-pulse delay-500"></div>
        <div class="absolute bottom-40 right-10 w-24 h-24 bg-pink-200 rounded-full opacity-50 animate-pulse delay-700"></div>
    </div>

    <script>
        function toggleRoleFields() {
            const roleSelect = document.getElementById('roleSelect');
            const patientFields = document.getElementById('patientFields');
            const medecinFields = document.getElementById('medecinFields');
            
            // Cacher tous les champs spécifiques
            patientFields.style.display = 'none';
            medecinFields.style.display = 'none';
            
            // Réinitialiser les champs requis
            resetRequiredFields();
            
            // Afficher les champs selon le rôle sélectionné
            if (roleSelect.value === 'patient') {
                patientFields.style.display = 'grid';
                // Rendre les champs patients requis
                document.getElementById('dateNaissance').required = true;
                document.getElementById('genre').required = true;
            } else if (roleSelect.value === 'medecin') {
                medecinFields.style.display = 'grid';
                // Rendre les champs médecins requis
                document.getElementById('specialite').required = true;
                document.getElementById('numeroLicence').required = true;
            }
        }
        
        function resetRequiredFields() {
            // Retirer l'attribut required de tous les champs spécifiques
            document.getElementById('dateNaissance').required = false;
            document.getElementById('genre').required = false;
            document.getElementById('specialite').required = false;
            document.getElementById('numeroLicence').required = false;
            
            // Vider les valeurs des champs cachés
            document.getElementById('dateNaissance').value = '';
            document.getElementById('genre').value = '';
            document.getElementById('specialite').value = '';
            document.getElementById('numeroLicence').value = '';
        }
        
        // Validation du formulaire avant soumission
        document.querySelector('form').addEventListener('submit', function(e) {
            const role = document.getElementById('roleSelect').value;
            
            if (role === 'patient') {
                const dateNaissance = document.getElementById('dateNaissance').value;
                const genre = document.getElementById('genre').value;
                
                if (!dateNaissance || !genre) {
                    e.preventDefault();
                    alert('Veuillez remplir tous les champs requis pour les patients.');
                    return false;
                }
            } else if (role === 'medecin') {
                const specialite = document.getElementById('specialite').value;
                const numeroLicence = document.getElementById('numeroLicence').value;
                
                if (!specialite || !numeroLicence) {
                    e.preventDefault();
                    alert('Veuillez remplir tous les champs requis pour les médecins.');
                    return false;
                }
            }
        });
    </script>
</body>
</html>
