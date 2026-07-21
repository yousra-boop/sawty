<?php
session_start();
require_once("auth/connexion.php");

// Guard Clause
if (!isset($_SESSION['user_name'])) {
    header("Location: index.php");
    exit();
}

// Handle resetting Massar code session
if (isset($_GET['action']) && $_GET['action'] === 'reset_code') {
    unset($_SESSION['verified_code']);
    header("Location: dashboard.php");
    exit();
}

// Fetch user's full name and surname from database based on session user_id
$user_id = $_SESSION['user_id'] ?? null;
$display_name = "Utilisateur";

if ($user_id) {
    $user_stmt = $pdo->prepare("SELECT user_name, user_surname FROM users WHERE id_user = ?");
    $user_stmt->execute([$user_id]);
    $user_data = $user_stmt->fetch(PDO::FETCH_ASSOC);
    if ($user_data) {
        $display_name = trim($user_data['user_name'] . ' ' . $user_data['user_surname']);
    }

    // Check if user has already submitted a candidacy application
    $cand_check_stmt = $pdo->prepare("SELECT id_candidat FROM candidats WHERE id_user = ?");
    $cand_check_stmt->execute([$user_id]);
    $user_candidacy = $cand_check_stmt->fetch(PDO::FETCH_ASSOC);
}

$msg_error = "";
$elections = [];
$candidates_by_election = [];
$voted_elections = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['code_massar'])) {
    $active_code = trim($_POST['code_massar']);
    $_SESSION['verified_code'] = $active_code;
} else {
    $active_code = $_SESSION['verified_code'] ?? null;
}

// If a code was provided via POST or session, fetch elections
if (!empty($active_code)) {
    $sql = "SELECT e.* FROM Elections e 
            JOIN ListesBlanches lb ON e.id_election = lb.id_election 
            WHERE lb.identifiant = :code";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':code', $active_code);
    $stmt->execute();
    $elections = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($elections)) {
        $msg_error = "Aucun scrutin ne correspond à ce Code Massar. Veuillez vérifier votre saisie ou contacter l'administration.";
    } else {
        $election_ids = array_column($elections, 'id_election');
        if (!empty($election_ids)) {
            $placeholders = implode(',', array_fill(0, count($election_ids), '?'));
            $cand_sql = "SELECT c.*, u.user_name, u.user_surname 
                         FROM Candidats c 
                         JOIN users u ON c.id_user = u.id_user 
                         WHERE c.id_election IN ($placeholders) AND c.c_status = 'approved'";
            $cand_stmt = $pdo->prepare($cand_sql);
            $cand_stmt->execute($election_ids);
            $all_candidates = $cand_stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($all_candidates as $cand) {
                $candidates_by_election[$cand['id_election']][] = $cand;
            }

            // Check votes_logs to see if this code already voted in these elections
            $vote_check_sql = "SELECT id_election FROM votes_logs WHERE national_id = ? AND id_election IN ($placeholders)";
            $vote_params = array_merge([$active_code], $election_ids);
            $vote_check_stmt = $pdo->prepare($vote_check_sql);
            $vote_check_stmt->execute($vote_params);
            $voted_rows = $vote_check_stmt->fetchAll(PDO::FETCH_ASSOC);
            $voted_elections = array_column($voted_rows, 'id_election');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sawty - Dashboard</title>
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
            <nav class="space-x-6 text-xs sm:text-sm font-bold flex items-center">
                <a href="#" class="text-gray-600 hover:text-royalPurple transition">Mon Profil</a>
                
                <?php if (!empty($user_candidacy)): ?>
                    <a href="candidat_dashboard.php" class="text-royalPurple hover:opacity-80 transition">Mon Espace Candidat</a>
                <?php endif; ?>

                <a href="candidat_signup.php" class="text-justiceGreen hover:opacity-80 transition">Devenir Candidat</a>
                <a href="index.php" class="text-red-600 hover:text-red-700 transition">Sign Out</a>
            </nav>
        </div>
    </header>

    <main class="flex-grow max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        
        <!-- Success Message Banner -->
        <?php if (isset($_GET['vote']) && $_GET['vote'] === 'success'): ?>
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-2xl p-4 text-sm font-bold flex items-center space-x-3 shadow-sm max-w-xl mx-auto">
                <span class="text-xl">✅</span>
                <p>Votre vote a été enregistré avec succès et de manière totalement anonyme !</p>
            </div>
        <?php endif; ?>

        <!-- Top Section: Check Eligibility Panel -->
        <?php if (empty($active_code) || !empty($msg_error)): ?>
            <section class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 max-w-xl mx-auto">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-sm text-royalPurple uppercase tracking-wider">Vérifier mon éligibilité</h3>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-justiceGreen"></span> Espace Sécurisé
                    </span>
                </div>

                <?php if (!empty($msg_error)): ?>
                    <div class="bg-rose-50 border border-rose-200 text-rose-700 p-3 rounded-xl text-xs mb-4 font-semibold">
                        <?php echo $msg_error; ?>
                    </div>
                <?php endif; ?>

                <form action="" method="POST" class="flex space-x-3">
                    <input type="text" name="code_massar" class="w-full border border-gray-200 px-4 py-2.5 rounded-xl text-xs focus:outline-none focus:border-justiceGreen transition" placeholder="Entrez votre Code Massar..." required>
                    <button type="submit" class="bg-royalPurple hover:bg-opacity-95 text-white font-bold text-xs px-6 py-2.5 rounded-xl transition shadow-sm whitespace-nowrap">Vérifier</button>
                </form>
            </section>
        <?php else: ?>
            <!-- Verified Header Bar showing User's Name with Reset Option -->
            <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-2xl max-w-xl mx-auto flex items-center justify-between">
                <div class="flex items-center space-x-2 text-xs font-bold text-emerald-900">
                    <span class="w-2.5 h-2.5 rounded-full bg-justiceGreen"></span>
                    <span>Bienvenue, <?php echo htmlspecialchars($display_name); ?></span>
                </div>
                <a href="dashboard.php?action=reset_code" class="text-xs font-bold text-royalPurple hover:underline bg-white px-3 py-1.5 rounded-lg border border-emerald-200 shadow-sm transition">
                    Changer d'identifiant
                </a>
            </div>
        <?php endif; ?>

        <!-- Main Content Area: Dynamic Elections & Side-by-Side Candidates -->
        <section class="space-y-6">
            <h2 class="text-base font-black text-royalPurple uppercase tracking-wide border-b border-gray-200 pb-3">Mes Scrutins Éligibles et Candidats</h2>

            <?php if (empty($active_code) || !empty($msg_error)): ?>
                <div class="bg-white p-12 rounded-2xl shadow-sm border border-gray-100 text-center text-gray-400 text-sm">
                    Veuillez entrer votre Code Massar ci-dessus pour afficher vos scrutins et les candidats correspondants.
                </div>
            <?php else: ?>
                <div class="space-y-6">
                    <?php foreach ($elections as $elec): 
                        $elec_id = $elec['id_election'];
                        $elec_candidates = $candidates_by_election[$elec_id] ?? [];
                        $has_voted = in_array($elec_id, $voted_elections);
                        $status = $elec['status'] ?? 'upcoming';
                    ?>
                        <!-- Card for each eligible election -->
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
                            
                            <!-- Election Info (Col 5) -->
                            <div class="md:col-span-5 flex items-center space-x-4">
                                <div class="shrink-0">
                                    <?php if (!empty($elec['poster'])): ?>
                                        <img src="uploads/<?php echo htmlspecialchars($elec['poster']); ?>" alt="Poster" class="w-16 h-16 object-cover rounded-xl shadow-sm border border-gray-100">
                                    <?php else: ?>
                                        <div class="w-16 h-16 bg-purple-50 text-royalPurple rounded-xl flex items-center justify-center text-xs font-bold">N/A</div>
                                    <?php endif; ?>
                                </div>
                                <div class="min-w-0">
                                    <h3 class="text-sm font-black text-royalPurple truncate"><?php echo htmlspecialchars($elec['e_title']); ?></h3>
                                    <p class="text-xs text-gray-400 font-mono mt-1">Début : <?php echo !empty($elec['start_date']) ? date('Y-m-d', strtotime($elec['start_date'])) : 'N/A'; ?></p>
                                    
                                    <div class="mt-3 flex flex-wrap items-center gap-2">
                                        <!-- Modal Trigger -->
                                        <button onclick="openElectionModal(<?php echo htmlspecialchars(json_encode($elec), ENT_QUOTES, 'UTF-8'); ?>)" 
                                            class="bg-gray-100 hover:bg-gray-200 text-royalPurple font-bold px-3 py-1.5 rounded-lg text-xs transition">
                                            Voir plus
                                        </button>

                                        <!-- Vote Status / Actions -->
                                        <?php if ($has_voted): ?>
                                            <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1">
                                                ✅ Déjà voté
                                            </span>
                                        <?php elseif ($status === 'active'): ?>
                                            <a href="voter_dashboard.php?id=<?php echo $elec['id_election']; ?>" 
                                               class="bg-justiceGreen hover:bg-opacity-95 text-white px-4 py-1.5 rounded-lg text-xs font-bold shadow-sm transition">
                                               Voter
                                            </a>
                                        <?php else: ?>
                                            <span class="bg-gray-100 text-gray-400 px-3 py-1.5 rounded-lg text-xs font-medium cursor-not-allowed">
                                                Bientôt ouvert
                                            </span>
                                        <?php endif; ?>

                                        <!-- Results Button (Always Clickable) -->
                                        <a href="resultats.php?id=<?php echo $elec_id; ?>" 
                                           class="bg-royalPurple hover:bg-opacity-90 text-white px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm transition">
                                            Voir les résultats
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Divider / Vertical Spacing -->
                            <div class="hidden md:block md:col-span-1 border-l border-gray-100 h-16 mx-auto"></div>

                            <!-- Candidates Specific to this Election (Col 6) -->
                            <div class="md:col-span-6 bg-gray-50 p-4 rounded-xl border border-gray-100">
                                <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-3">Candidats approuvés pour ce scrutin :</h4>
                                
                                <?php if (empty($elec_candidates)): ?>
                                    <p class="text-xs text-gray-400 italic">Aucun candidat approuvé pour le moment.</p>
                                <?php else: ?>
                                    <div class="flex flex-wrap gap-3 max-h-32 overflow-y-auto pr-1">
                                        <?php foreach ($elec_candidates as $cand): ?>
                                            <div class="flex items-center space-x-2 bg-white px-3 py-2 rounded-lg border border-gray-200 shadow-sm">
                                                <?php if (!empty($cand['c_photo'])): ?>
                                                    <img src="uploads/<?php echo htmlspecialchars($cand['c_photo']); ?>" alt="Photo" class="w-7 h-7 object-cover rounded-full border border-gray-100">
                                                <?php else: ?>
                                                    <div class="w-7 h-7 bg-emerald-100 text-emerald-800 rounded-full flex items-center justify-center text-[10px] font-bold">
                                                        <?php echo strtoupper(substr($cand['user_name'], 0, 1)); ?>
                                                    </div>
                                                <?php endif; ?>
                                                <span class="text-xs font-bold text-gray-800"><?php echo htmlspecialchars($cand['user_name'] . ' ' . $cand['user_surname']); ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 py-6 text-center text-xs text-gray-400">
        <div class="max-w-7xl mx-auto px-4">
            <p>&copy; 2026 SAWTY. Tous droits réservés.</p>
        </div>
    </footer>

    <!-- Election Details Modal -->
    <div id="election-modal" class="hidden fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto shadow-2xl p-6 border border-gray-100">
            <div class="flex justify-between items-start mb-4">
                <h3 id="modal-title" class="text-lg font-black text-royalPurple"></h3>
                <button onclick="closeElectionModal()" class="text-gray-400 hover:text-gray-600 font-bold text-lg">&times;</button>
            </div>

            <!-- Full Size Poster -->
            <div class="mb-4">
                <img id="modal-poster" src="" alt="Affiche" class="w-full h-48 object-cover rounded-xl shadow-inner border border-gray-100 hidden">
                <div id="modal-no-poster" class="w-full h-32 bg-gray-50 rounded-xl flex items-center justify-center text-xs text-gray-400 hidden">Aucune affiche disponible</div>
            </div>

            <!-- Dates info -->
            <div class="grid grid-cols-2 gap-2 mb-4 text-xs font-mono bg-purple-50 p-3 rounded-xl text-royalPurple">
                <div><strong>Début :</strong> <span id="modal-start"></span></div>
                <div><strong>Fin :</strong> <span id="modal-end"></span></div>
            </div>

            <!-- Description -->
            <div class="mb-6">
                <h4 class="text-xs font-bold uppercase text-gray-400 mb-1">Règlement & Description</h4>
                <p id="modal-desc" class="text-sm text-gray-600 whitespace-pre-line leading-relaxed"></p>
            </div>

            <div class="flex justify-end pt-2 border-t border-gray-100">
                <button onclick="closeElectionModal()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold px-4 py-2 rounded-xl transition">Fermer</button>
            </div>
        </div>
    </div>

    <!-- UI Interaction Scripts -->
    <script>
        function openElectionModal(elec) {
            document.getElementById('modal-title').textContent = elec.e_title;
            document.getElementById('modal-desc').textContent = elec.e_description || "Aucune description fournie pour cette élection.";
            document.getElementById('modal-start').textContent = elec.start_date ? elec.start_date.split(' ')[0] : '';
            document.getElementById('modal-end').textContent = elec.end_date ? elec.end_date.split(' ')[0] : '';
            const posterImg = document.getElementById('modal-poster');
            const noPosterDiv = document.getElementById('modal-no-poster');

            if (elec.poster) {
                posterImg.src = 'uploads/' + elec.poster;
                posterImg.classList.remove('hidden');
                noPosterDiv.classList.add('hidden');
            } else {
                posterImg.classList.add('hidden');
                noPosterDiv.classList.remove('hidden');
            }

            document.getElementById('election-modal').classList.remove('hidden');
        }

        function closeElectionModal() {
            document.getElementById('election-modal').classList.add('hidden');
        }
    </script>
</body>
</html>