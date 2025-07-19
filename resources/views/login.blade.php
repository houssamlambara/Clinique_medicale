<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="mx-auto w-16 h-16 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-user text-white text-2xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-800 mb-2">Connexion</h2>
            <p class="text-gray-600">Accédez à votre compte</p>
        </div>

        <!-- Form -->
        <form id="loginForm" class="space-y-6">
            @csrf

            <!-- Message d'erreur -->
            <div id="errorMessage" class="hidden bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                <p id="errorText"></p>
            </div>

            <!-- Message de succès -->
            <div id="successMessage" class="hidden bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                <p id="successText"></p>
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    required>
            </div>

            <!-- Mot de passe -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mot de passe</label>
                <input type="password" id="password" name="password"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    required>
            </div>

            <!-- Remember me -->
            <div class="flex items-center">
                <input type="checkbox" id="remember" name="remember"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="remember" class="ml-2 block text-sm text-gray-700">
                    Se souvenir de moi
                </label>
            </div>

            <button type="submit" id="loginButton"
                class="w-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white py-3 px-4 rounded-lg font-semibold hover:from-blue-600 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transform hover:scale-[1.02] transition duration-200 shadow-lg">
                <i class="fas fa-sign-in-alt mr-2"></i>
                <span id="buttonText">Se connecter</span>
            </button>
        </form>

        <!-- Register Link -->
        <div class="mt-8 text-center">
            <p class="text-sm text-gray-600">
                Vous n'avez pas de compte ?
                <a href="/register" class="text-blue-600 hover:text-blue-500 font-medium ml-1">
                    S'inscrire
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
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const remember = document.getElementById('remember').checked;
            
            const button = document.getElementById('loginButton');
            const buttonText = document.getElementById('buttonText');
            const errorMessage = document.getElementById('errorMessage');
            const successMessage = document.getElementById('successMessage');
            
            // Désactiver le bouton et montrer le chargement
            button.disabled = true;
            buttonText.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Connexion...';
            
            // Cacher les messages précédents
            errorMessage.classList.add('hidden');
            successMessage.classList.add('hidden');
            
            fetch('http://127.0.0.1:8000/api/auth/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    email: email,
                    password: password,
                    remember: remember
                })
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (data.success) {
                    console.log('Connexion réussie:', data);
                    
                    // Stocker le token
                    localStorage.setItem('auth_token', data.data.token);
                    localStorage.setItem('user_data', JSON.stringify(data.data.user));
                    
                    console.log('Token stocké:', data.data.token);
                    console.log('Données utilisateur stockées:', data.data.user);
                    
                    // Rediriger selon le rôle
                    setTimeout(function() {
                        const user = data.data.user;
                        console.log('Redirection vers:', user.role);
                        
                        if (user.role === 'patient') {
                            console.log('Redirection vers /patient/dashboard');
                            // Essayer plusieurs méthodes de redirection
                            try {
                                window.location.href = '/patient/dashboard';
                            } catch (e) {
                                console.error('Erreur redirection:', e);
                                window.location.replace('/patient/dashboard');
                            }
                        } else if (user.role === 'medecin') {
                            console.log('Redirection vers /medecin/dashboard');
                            window.location.href = '/medecin/dashboard';
                        } else if (user.role === 'secretaire') {
                            console.log('Redirection vers /secretaire/dashboard');
                            window.location.href = '/secretaire/dashboard';
                        } else if (user.role === 'comptable') {
                            console.log('Redirection vers /comptable/dashboard');
                            window.location.href = '/comptable/dashboard';
                        } else {
                            console.log('Redirection vers /dashboard');
                            window.location.href = '/dashboard';
                        }
                    }, 1500);
                    
                } else {
                    // Afficher l'erreur
                    document.getElementById('errorText').textContent = data.message || 'Erreur de connexion';
                    errorMessage.classList.remove('hidden');
                }
            })
            .catch(function(error) {
                console.error('Erreur:', error);
                document.getElementById('errorText').textContent = 'Erreur de connexion au serveur';
                errorMessage.classList.remove('hidden');
            })
            .finally(function() {
                // Réactiver le bouton
                button.disabled = false;
                buttonText.innerHTML = '<i class="fas fa-sign-in-alt mr-2"></i>Se connecter';
            });
        });
    </script>
</body>

</html>