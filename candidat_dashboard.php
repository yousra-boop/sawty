<?php
session_start();
require_once("auth/connexion.php");

// Guard Clause: Ensure user is logged in
if (!isset($_SESSION['user_name'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header("Location: dashboard.php");
    exit();
}

// Fetch user data and candidacy information
$stmt = $pdo->prepare("
    SELECT c.*, e.e_title, e.start_date, e.end_date, e.status as election_status, u.user_name, u.user_surname, u.user_email 
    FROM candidats c 
    JOIN elections e ON c.id_election = e.id_election 
    JOIN users u ON c.id_user = u.id_user 
    WHERE c.id_user = ?
");
$stmt->execute([$user_id]);
$candidacy = $stmt->fetch(PDO::FETCH_ASSOC);

// If the user hasn't registered as a candidate yet, redirect them to the signup page
if (!$candidacy) {
    header("Location: candidat_signup.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sawty - Espace Candidat</title>
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
                <span class="text-xl font-black tracking-wider text-royalPurple">SAWTY<span class="text-justiceGreen">.</span> <span class="text-xs uppercase bg-purple-50 text-royalPurple px-2 py-0.5 rounded-md border border-purple-100 font-bold ml-2">Candidat</span></span>
            </div>
            <nav class="space-x-6 text-xs sm:text-sm font-bold flex items-center">
                <a href="dashboard.php" class="text-gray-600 hover:text-royalPurple transition">&larr; Retour au Tableau de Bord</a>
                <a href="index.php" class="text-red-600 hover:text-red-700 transition">Sign Out</a>
            </nav>
        </div>
    </header>

    <main class="flex-grow max-w-4xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        
        <!-- Page Title & Status Banner -->
       <!-- Page Title & Status Banner -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between bg-white p-6 rounded-2xl shadow-sm border border-gray-100 gap-4">
            <div>
                <h1 class="text-2xl font-black text-royalPurple">Mon Dossier de Candidature</h1>
                <p class="text-xs text-gray-400 mt-1">Scrutin visé : <span class="font-bold text-gray-700"><?php echo htmlspecialchars($candidacy['e_title']); ?></span></p>
            </div>
            <div>
                <?php if ($candidacy['c_status'] === 'approved'): ?>
                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-justiceGreen"></span> Candidature Approuvée
                    </span>
                <?php elseif ($candidacy['c_status'] === 'rejected'): ?>
                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span> Candidature Rejetée
                    </span>
                <?php else: ?>
                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span> En Attente de Validation
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Rejection Notice Box (Appears only if rejected) -->
        <?php if ($candidacy['c_status'] === 'rejected'): ?>
            <div class="bg-rose-50 border border-rose-200 text-rose-900 rounded-2xl p-4 text-xs font-semibold flex items-start space-x-3 shadow-sm">
                <span class="text-base">⚠️</span>
                <div>
                    <p class="font-bold">Votre candidature n'a pas été retenue.</p>
                    <p class="text-rose-700 mt-0.5">Si vous pensez qu'il s'agit d'une erreur, veuillez contacter l'administration.</p>
                </div>
            </div>
        <?php endif; ?>
        <!-- Main Details Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8 space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center border-b border-gray-100 pb-6">
                <!-- Candidate Media / Photo Preview -->
                <div class="flex flex-col items-center justify-center space-y-3">
                    <?php if (!empty($candidacy['c_photo'])): ?>
                        <img src="../<?php echo htmlspecialchars($candidacy['c_photo']); ?>" alt="Photo de profil" class="w-28 h-28 object-cover rounded-2xl border-2 border-purple-100 shadow-md">
                    <?php else: ?>
                        <div class="w-28 h-28 bg-purple-50 text-royalPurple rounded-2xl flex items-center justify-center font-black text-2xl border-2 border-purple-100">
                            <?php echo strtoupper(substr($candidacy['user_name'], 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                    <p class="text-xs font-bold text-gray-700"><?php echo htmlspecialchars($candidacy['user_name'] . ' ' . $candidacy['user_surname']); ?></p>
                </div>

                <!-- Candidacy Meta Info -->
                <div class="md:col-span-2 space-y-3 text-xs">
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 space-y-2">
                        <p class="text-gray-500"><strong>Email :</strong> <?php echo htmlspecialchars($candidacy['user_email']); ?></p>
                        <p class="text-gray-500"><strong>Statut du Scrutin :</strong> <span class="uppercase font-bold text-royalPurple"><?php echo htmlspecialchars($candidacy['election_status']); ?></span></p>
                        <p class="text-gray-500"><strong>Période :</strong> Du <?php echo htmlspecialchars($candidacy['start_date']); ?> au <?php echo htmlspecialchars($candidacy['end_date']); ?></p>
                    </div>

                    <?php if (!empty($candidacy['c_video'])): ?>
                        <div>
                            <a href="../<?php echo htmlspecialchars($candidacy['c_video']); ?>" target="_blank" class="inline-flex items-center gap-2 bg-purple-50 text-royalPurple hover:bg-purple-100 font-bold px-4 py-2 rounded-xl transition border border-purple-100">
                                🎥 Visionner votre capsule vidéo de présentation
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Bio / Program Section -->
            <div class="space-y-2">
                <h3 class="text-xs font-bold uppercase text-gray-400 tracking-wider">Votre Biographie & Profession de Foi</h3>
                <div class="bg-gray-50 p-5 rounded-xl border border-gray-100 text-sm text-gray-700 leading-relaxed whitespace-pre-line">
                    <?php echo !empty($candidacy['c_bio']) ? htmlspecialchars($candidacy['c_bio']) : "Aucune biographie renseignée."; ?>
                </div>
            </div>

        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 py-6 text-center text-xs text-gray-400">
        <div class="max-w-7xl mx-auto px-4">
            <p>&copy; 2026 SAWTY. Espace Candidat.</p>
        </div>
    </footer>

</body>
</html>