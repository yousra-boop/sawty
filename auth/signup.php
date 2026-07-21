<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sawty - Créer un compte</title>
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

    <main class="flex-grow flex items-center justify-center p-4 sm:p-6 lg:p-8">
        <div class="max-w-4xl w-full bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden grid grid-cols-1 md:grid-cols-12">
            
            <!-- Left Side: Form Section (Col 7) -->
            <section class="md:col-span-7 p-8 sm:p-10 flex flex-col justify-center space-y-6">
                <div>
                    <h2 class="text-2xl font-black text-royalPurple">Créer votre compte</h2>
                    <p class="text-xs text-gray-400 mt-1">Rejoignez la plateforme et participez aux scrutins en toute sécurité.</p>
                </div>

                <form action="signupinsert.php" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                    
                    <!-- First Name & Last Name Row -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="font-bold text-gray-700">Prénom</label>
                            <input type="text" name="user_name" required class="w-full border border-gray-200 px-3.5 py-2.5 rounded-xl focus:outline-none focus:border-royalPurple transition">
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-gray-700">Nom</label>
                            <input type="text" name="user_surname" required class="w-full border border-gray-200 px-3.5 py-2.5 rounded-xl focus:outline-none focus:border-royalPurple transition">
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="space-y-1">
                        <label class="font-bold text-gray-700">Email Étudiant</label>
                        <input type="email" name="user_email" required class="w-full border border-gray-200 px-3.5 py-2.5 rounded-xl focus:outline-none focus:border-royalPurple transition" placeholder="nom@etudiant.ma">
                    </div>

                    <!-- Phone & National ID Row -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="font-bold text-gray-700">Téléphone</label>
                            <input type="text" name="user_phone" required class="w-full border border-gray-200 px-3.5 py-2.5 rounded-xl focus:outline-none focus:border-royalPurple transition" placeholder="06XXXXXXXX">
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-gray-700">National ID (Code Massar)</label>
                            <input type="text" name="national_id" required class="w-full border border-gray-200 px-3.5 py-2.5 rounded-xl focus:outline-none focus:border-royalPurple transition" placeholder="Ex: M123456789">
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="space-y-1">
                        <label class="font-bold text-gray-700">Mot de passe</label>
                        <input type="password" name="user_password" required class="w-full border border-gray-200 px-3.5 py-2.5 rounded-xl focus:outline-none focus:border-royalPurple transition" placeholder="••••••••">
                    </div>

                    <!-- Profile Avatar Upload -->
                    <div class="space-y-1">
                        <label class="font-bold text-gray-700">Photo de profil</label>
                        <input type="file" name="user_avatar" accept="image/*" required class="w-full text-gray-500 text-xs file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-purple-50 file:text-royalPurple hover:file:bg-purple-100 transition cursor-pointer">
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-justiceGreen hover:bg-opacity-95 text-white font-bold py-3 px-4 rounded-xl transition shadow-md mt-2">
                        S'inscrire
                    </button>
                </form>

                <p class="text-center text-xs text-gray-400">
                    Déjà un compte ? <a href="../index.php" class="font-bold text-royalPurple hover:underline">Se connecter</a>
                </p>
            </section>

            <!-- Right Side: Engagement / Pledge Info (Col 5) -->
            <section class="md:col-span-5 bg-gradient-to-br from-purple-900 to-royalPurple p-8 sm:p-10 text-white flex flex-col justify-between space-y-6 relative overflow-hidden">
                <!-- Decorative Circle Glow -->
                <div class="absolute -right-12 -bottom-12 w-48 h-48 bg-emerald-500 rounded-full blur-3xl opacity-20 pointer-events-none"></div>

                <div class="space-y-4 relative z-10">
                    <div class="flex items-center space-x-2">
                        <svg class="w-8 h-8 text-white" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="24" cy="20" r="6" fill="currentColor"/>
                            <path d="M12 44C12 35.1634 19.1634 28 28 28H30C33.123 28 36.0345 28.8929 38.5 30.435" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
                            <path d="M16 46H44V60H16V46Z" stroke="currentColor" stroke-width="4" stroke-linejoin="round"/>
                            <path d="M34 24L46 32L38 40" fill="#059669" opacity="0.8"/>
                            <rect x="34" y="24" width="16" height="12" rx="1" transform="rotate(-15 34 24)" stroke="currentColor" stroke-width="3" fill="white"/>
                        </svg>
                        <span class="text-2xl font-black tracking-wider text-white">SAWTY<span class="text-emerald-400">.</span></span>
                    </div>

                    <div class="space-y-2 pt-4">
                        <h3 class="text-lg font-black tracking-wide">L'engagement Sawty</h3>
                        <p class="text-xs text-purple-200 leading-relaxed">En créant ce compte, vous rejoignez une plateforme dédiée à la transparence électorale et à la démocratie participative.</p>
                    </div>
                </div>

                <ul class="space-y-3 text-xs text-purple-100 relative z-10">
                    <li class="flex items-center space-x-2.5">
                        <span class="w-5 h-5 bg-emerald-500/20 text-emerald-400 rounded-full flex items-center justify-center font-bold text-[10px]">✓</span>
                        <span>Intégrité totale des données.</span>
                    </li>
                    <li class="flex items-center space-x-2.5">
                        <span class="w-5 h-5 bg-emerald-500/20 text-emerald-400 rounded-full flex items-center justify-center font-bold text-[10px]">✓</span>
                        <span>Confidentialité absolue du vote.</span>
                    </li>
                    <li class="flex items-center space-x-2.5">
                        <span class="w-5 h-5 bg-emerald-500/20 text-emerald-400 rounded-full flex items-center justify-center font-bold text-[10px]">✓</span>
                        <span>Vérification sécurisée par identifiant.</span>
                    </li>
                </ul>

                <div class="text-[11px] text-purple-300 relative z-10 pt-4 border-t border-purple-800/60">
                    &copy; 2026 SAWTY. Tous droits réservés.
                </div>
            </section>

        </div>
    </main>

</body>
</html>