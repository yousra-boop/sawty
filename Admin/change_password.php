<?php
session_start();
require_once("../auth/connexion.php");

// Ensure the user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$error_msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    try {
        $stmt = $pdo->prepare("UPDATE Admins SET admin_password = ?, is_temp = 0 WHERE id_admin = ?");
        $stmt->execute([$new_pass, $_SESSION['admin_id']]);
        
        $_SESSION['is_temp'] = 0; // Update session
        header("Location: admin_dashboard.php");
        exit();
    } catch (PDOException $e) {
        $error_msg = "Erreur de mise à jour : " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sawty - Nouveau mot de passe</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        darkBlue: '#0f172a',
                        cardBlue: '#1e293b',
                        accentBlue: '#3b82f6',
                        justiceGreen: '#059669',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-darkBlue font-sans min-h-screen flex flex-col justify-between text-gray-100">

    <!-- Top Header Navigation -->
    <header class="bg-cardBlue border-b border-gray-800 shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <svg class="w-8 h-8 text-accentBlue" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="24" cy="20" r="6" fill="currentColor"/>
                    <path d="M12 44C12 35.1634 19.1634 28 28 28H30C33.123 28 36.0345 28.8929 38.5 30.435" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
                    <path d="M16 46H44V60H16V46Z" stroke="currentColor" stroke-width="4" stroke-linejoin="round"/>
                    <path d="M34 24L46 32L38 40" fill="#059669" opacity="0.8"/>
                    <rect x="34" y="24" width="16" height="12" rx="1" transform="rotate(-15 34 24)" stroke="currentColor" stroke-width="3" fill="#1e293b"/>
                </svg>
                <span class="text-xl font-black tracking-wider text-white">SAWTY<span class="text-justiceGreen">.</span></span>
            </div>
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Sécurité Administrateur</span>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-grow flex items-center justify-center p-4 sm:p-6 lg:p-8">
        <div class="max-w-md w-full bg-cardBlue p-8 rounded-3xl shadow-xl border border-gray-800 space-y-6">
            
            <div class="space-y-1 text-center">
                <h2 class="text-xl font-black text-white">Mise à jour du mot de passe</h2>
                <p class="text-xs text-gray-400">Veuillez définir un nouveau mot de passe sécurisé pour continuer.</p>
            </div>

            <?php if (!empty($error_msg)): ?>
                <div class="bg-rose-950/50 border border-rose-800 text-rose-200 p-3 rounded-xl text-xs font-semibold">
                    <?php echo htmlspecialchars($error_msg); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider">Nouveau mot de passe</label>
                    <input type="password" name="password" class="w-full bg-darkBlue border border-gray-700 px-4 py-3 rounded-xl text-xs text-white placeholder-gray-500 focus:outline-none focus:border-accentBlue transition" placeholder="••••••••" required>
                </div>
                
                <button type="submit" class="w-full bg-accentBlue hover:bg-opacity-90 text-white font-bold text-xs py-3.5 px-4 rounded-xl transition shadow-md">
                    Mettre à jour & Accéder au tableau de bord
                </button>
            </form>

        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-cardBlue border-t border-gray-800 py-6 text-center text-xs text-gray-500">
        <div class="max-w-7xl mx-auto px-4">
            <p>&copy; 2026 SAWTY. Tous droits réservés.</p>
        </div>
    </footer>

</body>
</html>