<?php
session_start();
// Optional error handling display if redirected back with errors
$error_msg = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sawty - Connexion</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        royalPurple: '#4c1d95',
                        justiceGreen: '#059669',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 font-sans min-h-screen flex flex-col justify-between text-gray-800">

    <!-- Top Header Navigation -->
    <header class="bg-white border-b border-gray-100 shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <svg class="w-8 h-8 text-royalPurple" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="24" cy="20" r="6" fill="currentColor"/>
                    <path d="M12 44C12 35.1634 19.1634 28 28 28H30C33.123 28 36.0345 28.8929 38.5 30.435" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
                    <path d="M16 46H44V60H16V46Z" stroke="currentColor" stroke-width="4" stroke-linejoin="round"/>
                    <path d="M34 24L46 32L38 40" fill="#059669" opacity="0.8"/>
                    <rect x="34" y="24" width="16" height="12" rx="1" transform="rotate(-15 34 24)" stroke="currentColor" stroke-width="3" fill="white"/>
                </svg>
                <span class="text-xl font-black tracking-wider text-royalPurple">SAWTY<span class="text-justiceGreen">.</span></span>
            </div>
            <nav class="text-xs font-bold text-gray-500 hover:text-royalPurple transition cursor-pointer">Comment ça marche ?</nav>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-grow max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-12 grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
        
        <!-- Partie Gauche (Intro Info) -->
        <section class="lg:col-span-7 space-y-6">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-purple-50 text-royalPurple border border-purple-100">
                <span class="w-2 h-2 rounded-full bg-justiceGreen"></span> Transparence & Intégrité Totale
            </div>
            
            <h1 class="text-3xl sm:text-4xl font-black text-gray-900 leading-tight">
                Chaque vote est une enveloppe pour <span class="text-royalPurple">faire entendre votre voix.</span>
            </h1>
            
            <p class="text-sm sm:text-base text-gray-600 leading-relaxed">
                Bienvenue sur Sawty, le système d'élections sécurisé qui garantit à la fois la confidentialité absolue de votre choix et la vérification stricte de votre participation.
            </p>
            
            <hr class="border-gray-100 my-6">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm space-y-2">
                    <h3 class="font-bold text-sm text-royalPurple">Anonymat Garanti</h3>
                    <p class="text-xs text-gray-500 leading-relaxed">Le contenu de votre enveloppe ne contient aucun lien direct avec votre identité.</p>
                </div>
                
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm space-y-2">
                    <h3 class="font-bold text-sm text-royalPurple">Droit de Protestation</h3>
                    <p class="text-xs text-gray-500 leading-relaxed">Exprimez officiellement votre désaccord via le vote blanc constructif.</p>
                </div>
            </div>
        </section>

        <!-- Formulaire de connexion (Partie Droite) -->
        <section class="lg:col-span-5 bg-white p-8 rounded-3xl shadow-sm border border-gray-100 space-y-6">
            <div class="space-y-1">
                <h2 class="text-xl font-black text-gray-900">Espace Électoral</h2>
                <p class="text-xs text-gray-400 font-medium">Connectez-vous pour accéder à votre espace de vote</p>
            </div>

            <?php if (!empty($error_msg)): ?>
                <div class="bg-rose-50 border border-rose-200 text-rose-700 p-3 rounded-xl text-xs font-semibold">
                    <?php echo htmlspecialchars($error_msg); ?>
                </div>
            <?php endif; ?>
            
            <form action="auth/logincheck.php" method="POST" class="space-y-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Adresse Email Étudiante</label>
                    <input type="email" name="user_email" class="w-full border border-gray-200 px-4 py-3 rounded-xl text-xs focus:outline-none focus:border-royalPurple transition" placeholder="nom@etudiant.ma" required>
                </div>
                
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Mot de passe</label>
                    <input type="password" name="user_password" class="w-full border border-gray-200 px-4 py-3 rounded-xl text-xs focus:outline-none focus:border-royalPurple transition" placeholder="••••••••" required>
                </div>
                
                <button type="submit" class="w-full bg-royalPurple hover:bg-opacity-95 text-white font-bold text-xs py-3.5 px-4 rounded-xl transition shadow-md">
                    Accéder au bureau de vote
                </button>
            </form>
            
            <p class="text-center text-xs text-gray-500 pt-2">
                Nouveau sur la plateforme ? <a href="auth/signup.php" class="text-royalPurple font-bold hover:underline">Créer un compte</a>
            </p>
        </section>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 py-6 text-center text-xs text-gray-400">
        <div class="max-w-7xl mx-auto px-4">
            <p>&copy; 2026 SAWTY. Tous droits réservés. | <a href="Admin/admin_login.php" class="hover:text-royalPurple transition">Admin Login</a></p>
        </div>
    </footer>

</body>
</html>