<?php
session_start();
require_once("auth/connexion.php");

// Guard Clause: Ensure user is logged in
if (!isset($_SESSION['user_name'])) {
    header("Location: index.php");
    exit();
}

$election_id = $_GET['id'] ?? null;

if (!$election_id) {
    header("Location: dashboard.php");
    exit();
}

// Fetch election details
$elec_stmt = $pdo->prepare("SELECT * FROM elections WHERE id_election = ?");
$elec_stmt->execute([$election_id]);
$election = $elec_stmt->fetch(PDO::FETCH_ASSOC);

if (!$election) {
    header("Location: dashboard.php");
    exit();
}

// Check if election status is closed
$status = $election['status'] ?? 'active';
$is_closed = ($status === 'closed');

$candidates = [];
$abstains = [];
$total_abstains = 0;

if ($is_closed) {
    // Fetch candidates and their vote counts from the 'enveloppes' table
    $cand_sql = "SELECT c.*, u.user_name, u.user_surname, 
                 (SELECT COUNT(*) FROM envloppes e WHERE e.id_election = c.id_election AND e.id_candidat = c.id_candidat) AS vote_count
                 FROM candidats c 
                 JOIN users u ON c.id_user = u.id_user 
                 WHERE c.id_election = ? AND c.c_status = 'approved'";
    $cand_stmt = $pdo->prepare($cand_sql);
    $cand_stmt->execute([$election_id]);
    $candidates = $cand_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch abstentions/protests and comments from the 'enveloppes' table where user_protest is active
    $abstain_sql = "SELECT * FROM envloppes WHERE id_election = ? AND user_protest = 1";
    $abstain_stmt = $pdo->prepare($abstain_sql);
    $abstain_stmt->execute([$election_id]);
    $abstains = $abstain_stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_abstains = count($abstains);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sawty - Résultats de l'élection</title>
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
                <span class="text-xl font-black tracking-wider text-royalPurple">SAWTY<span class="text-justiceGreen">.</span></span>
            </div>
            <nav class="space-x-6 text-xs sm:text-sm font-bold flex items-center">
                <a href="dashboard.php" class="text-gray-600 hover:text-royalPurple transition">Tableau de Bord</a>
                <a href="deconnexion.php" class="text-red-600 hover:text-red-700 transition">Sign Out</a>
            </nav>
        </div>
    </header>

    <main class="flex-grow max-w-4xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        
        <!-- Back Button -->
        <div>
            <a href="dashboard.php" class="text-xs font-bold text-royalPurple hover:underline flex items-center gap-1">
                &larr; Retour au tableau de bord
            </a>
        </div>

        <!-- Election Results Card -->
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 space-y-6">
            
            <div class="border-b border-gray-100 pb-6">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-purple-50 text-royalPurple mb-2">Résultats Officiels</span>
                <h1 class="text-2xl font-black text-royalPurple"><?php echo htmlspecialchars($election['e_title']); ?></h1>
                <p class="text-xs text-gray-400 mt-1">Scrutin : <?php echo htmlspecialchars($election['e_title']); ?></p>
            </div>

            <?php if (!$is_closed): ?>
                <!-- Results Not Out Yet State -->
                <div class="bg-amber-50 border border-amber-200 text-amber-900 rounded-2xl p-8 text-center space-y-3">
                    <span class="text-3xl">⏳</span>
                    <h2 class="text-base font-bold">Résultats bientôt disponibles</h2>
                    <p class="text-xs text-amber-700 max-w-md mx-auto">Ce scrutin est toujours en cours ou n'a pas encore été clôturé par l'administration. Veuillez revenir plus tard pour consulter les résultats officiels.</p>
                </div>
            <?php else: ?>
                <!-- Candidates Results Table -->
                <div class="space-y-4">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-gray-400">Classement des Candidats</h2>
                    
                    <div class="overflow-x-auto border border-gray-100 rounded-xl">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 text-xs font-bold text-gray-500 uppercase border-b border-gray-100">
                                    <th class="p-4">Candidat</th>
                                    <th class="p-4 text-center">Voix obtenues</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-xs">
                                <?php if (empty($candidates)): ?>
                                    <tr>
                                        <td colspan="2" class="p-4 text-center text-gray-400 italic">Aucun candidat enregistré pour ce scrutin.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($candidates as $cand): ?>
                                        <tr class="hover:bg-gray-50/50 transition">
                                            <td class="p-4 flex items-center space-x-3">
                                                <?php if (!empty($cand['c_photo'])): ?>
                                                    <img src="uploads/<?php echo htmlspecialchars($cand['c_photo']); ?>" alt="Photo" class="w-8 h-8 object-cover rounded-full border border-gray-100">
                                                <?php else: ?>
                                                    <div class="w-8 h-8 bg-purple-100 text-royalPurple rounded-full flex items-center justify-center font-bold text-xs">
                                                        <?php echo strtoupper(substr($cand['user_name'], 0, 1)); ?>
                                                    </div>
                                                <?php endif; ?>
                                                <span class="font-bold text-gray-800"><?php echo htmlspecialchars($cand['user_name'] . ' ' . $cand['user_surname']); ?></span>
                                            </td>
                                            <td class="p-4 text-center font-mono font-bold text-royalPurple text-sm">
                                                <?php echo $cand['vote_count']; ?> voix
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Abstentions and Comments Section -->
                <div class="space-y-4 pt-4 border-t border-gray-100">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-bold uppercase tracking-wider text-gray-400">Abstentions & Commentaires</h2>
                        <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-bold">Total abstentions : <?php echo $total_abstains; ?></span>
                    </div>

                    <?php if (empty($abstains)): ?>
                        <p class="text-xs text-gray-400 italic">Aucune abstention ou commentaire enregistré.</p>
                    <?php else: ?>
                        <div class="space-y-3 max-h-60 overflow-y-auto pr-2">
                            <?php foreach ($abstains as $abstain): ?>
                                <?php if (!empty($abstain['protest_reason'])): ?>
                                    <div class="bg-gray-50 border border-gray-100 p-4 rounded-xl space-y-1">
                                        <p class="text-xs text-gray-700 italic">"<?php echo htmlspecialchars($abstain['protest_reason']); ?>"</p>
                                        <p class="text-[10px] text-gray-400 font-mono">Enregistré anonymement</p>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 py-6 text-center text-xs text-gray-400">
        <div class="max-w-7xl mx-auto px-4">
            <p>&copy; 2026 SAWTY. Tous droits réservés.</p>
        </div>
    </footer>

</body>
</html>