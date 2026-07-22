<?php
session_start();
require_once("../auth/connexion.php");

// Guard Clause: Ensure admin is logged in using the correct session key
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

// Fetch only the latest single pending candidate ordered by id_candidat descending
$sql = "SELECT c.*, u.user_name, u.user_surname, u.user_email, u.national_id, u.user_avatar, e.e_title 
        FROM candidats c 
        JOIN users u ON c.id_user = u.id_user 
        JOIN elections e ON c.id_election = e.id_election 
        WHERE c.c_status = 'pending' 
        ORDER BY c.id_candidat DESC 
        LIMIT 1";
$stmt = $pdo->query($sql);
$latest_pending_candidate = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sawty - Dernière Candidature en Attente</title>
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
                <span class="text-xl font-black tracking-wider text-royalPurple">SAWTY<span class="text-justiceGreen">.</span> Admin</span>
            </div>
            <nav class="space-x-6 text-xs sm:text-sm font-bold flex items-center">
                <a href="admin_dashboard.php" class="text-gray-600 hover:text-royalPurple transition">Tableau de bord</a>
                <a href="pending_candidates.php" class="text-royalPurple hover:underline transition">Voir toutes les attente</a>
                <a href="../deconnexion.php" class="text-red-600 hover:text-red-700 transition">Sign Out</a>
            </nav>
        </div>
    </header>

    <main class="flex-grow max-w-4xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        
        <!-- Page Title Header -->
        <div class="flex items-center justify-between border-b border-gray-200 pb-4">
            <div>
                <h1 class="text-2xl font-black text-royalPurple">Dernière Candidature Soumise</h1>
                <p class="text-xs text-gray-400 mt-1">Examen rapide de la soumission la plus récente en attente de validation.</p>
            </div>
            <span class="bg-amber-50 text-amber-800 font-bold px-3 py-1.5 rounded-full text-xs border border-amber-200">
                Dernière entrée
            </span>
        </div>

        <!-- Single Candidate Focus Box -->
        <?php if (!$latest_pending_candidate): ?>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center text-gray-400 italic space-y-2">
                <span class="text-3xl block">✨</span>
                <p class="font-bold text-gray-600">Aucune candidature en attente</p>
                <p class="text-[11px] text-gray-400">Il n'y a actuellement aucune nouvelle demande à traiter.</p>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8 space-y-6">
                
                <!-- Candidate Header Info -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-gray-100 pb-6 gap-4">
                    <div class="flex items-center space-x-4">
                        <?php if (!empty($latest_pending_candidate['user_avatar'])): ?>
                            <img src="../<?php echo htmlspecialchars($latest_pending_candidate['user_avatar']); ?>" alt="Avatar" class="w-14 h-14 object-cover rounded-full border border-gray-200 shadow-sm">
                        <?php else: ?>
    <div class="w-14 h-14 bg-purple-100 text-royalPurple rounded-full flex items-center justify-center font-bold text-base">
        <?php echo strtoupper(substr($latest_pending_candidate['user_name'], 0, 1)); ?>
    </div>
<?php endif; ?>
                        <div>
                            <h2 class="text-lg font-black text-gray-900"><?php echo htmlspecialchars($latest_pending_candidate['user_name'] . ' ' . $latest_pending_candidate['user_surname']); ?></h2>
                            <p class="text-xs text-gray-400"><?php echo htmlspecialchars($latest_pending_candidate['user_email']); ?></p>
                        </div>
                    </div>
                    <span class="inline-block bg-purple-50 text-royalPurple px-3 py-1.5 rounded-xl font-bold text-xs border border-purple-100">
                        Scrutin : <?php echo htmlspecialchars($latest_pending_candidate['e_title']); ?>
                    </span>
                </div>

                <!-- Details Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-xs">
                    <div class="space-y-4 md:col-span-2">
                        <div>
                            <h4 class="font-bold text-gray-400 uppercase tracking-wider text-[11px] mb-1">Code Massar :</h4>
                            <p class="font-mono font-bold text-gray-700"><?php echo htmlspecialchars($latest_pending_candidate['national_id'] ?? 'N/A'); ?></p>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-400 uppercase tracking-wider text-[11px] mb-1">Biographie / Profession de foi :</h4>
                            <p class="text-gray-600 bg-gray-50 p-4 rounded-xl border border-gray-100 leading-relaxed whitespace-pre-line">
                                <?php echo htmlspecialchars($latest_pending_candidate['c_bio'] ?: 'Aucune bio fournie.'); ?>
                            </p>
                        </div>
                    </div>

                    <!-- Media Panel -->
                    <div class="bg-purple-50/50 p-4 rounded-2xl border border-purple-100 space-y-4 flex flex-col justify-between">
                        <div class="space-y-3">
                            <h4 class="font-bold text-royalPurple uppercase tracking-wider text-[11px]">Médias Soumis</h4>
                            
                            <!-- Photo -->
                           <!-- Photo -->
<div>
    <p class="text-[11px] text-gray-500 mb-1">Photo officielle :</p>
    <?php if (!empty($latest_pending_candidate['c_photo'])): ?>
        <?php 
            // Normalize path to prevent double '../' issues
            $photoPath = $latest_pending_candidate['c_photo'];
            $finalPhoto = (strpos($photoPath, '../') === 0) ? $photoPath : '../' . ltrim($photoPath, '/');
        ?>
        <a href="<?php echo htmlspecialchars($finalPhoto); ?>" target="_blank">
            <img src="<?php echo htmlspecialchars($finalPhoto); ?>" alt="Photo de campagne" class="w-full h-32 object-cover rounded-xl border border-gray-200 shadow-sm hover:scale-[1.02] transition">
        </a>
    <?php else: ?>
        <p class="text-[11px] text-gray-400 italic">Aucune photo fournie</p>
    <?php endif; ?>
</div>

                            <!-- Video -->
                            <?php if (!empty($latest_pending_candidate['c_video'])): ?>
                                <div>
                                    <p class="text-[11px] text-gray-500 mb-1">Capsule Vidéo :</p>
                                    <a href="../<?php echo htmlspecialchars($latest_pending_candidate['c_video']); ?>" target="_blank" class="block bg-white hover:bg-gray-50 text-royalPurple font-bold p-2.5 rounded-xl text-center text-xs border border-purple-200 transition shadow-sm">
                                        🎥 Visionner la vidéo
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Action Buttons Form -->
                        <div class="pt-4 border-t border-purple-100">
                            <form action="approve_candidate.php" method="POST" class="flex gap-2">
                                <input type="hidden" name="id_candidat" value="<?php echo $latest_pending_candidate['id_candidat']; ?>">
                                <button type="submit" name="action" value="approve" 
                                    class="flex-1 bg-justiceGreen hover:bg-opacity-90 text-white font-bold py-2 px-3 rounded-xl text-xs shadow-sm transition text-center">
                                    Approuver
                                </button>
                                <button type="submit" name="action" value="reject" 
                                    class="flex-1 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold py-2 px-3 rounded-xl text-xs transition text-center">
                                    Rejeter
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        <?php endif; ?>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 py-6 text-center text-xs text-gray-400">
        <div class="max-w-7xl mx-auto px-4">
            <p>&copy; 2026 SAWTY. Espace Administration.</p>
        </div>
    </footer>

</body>
</html>