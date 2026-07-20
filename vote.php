<?php
session_start();
// Replace with your actual path
require_once("auth/connexion.php");

// Fetch Election & Candidates
$id_election = $_GET['id'] ?? 0;
$election = $pdo->prepare("SELECT * FROM Elections WHERE id_election = ?");
$election->execute([$id_election]);
$elec = $election->fetch(PDO::FETCH_ASSOC);

if (!$elec) {
    die("Cette élection n'existe pas ou est fermée.");
}

$stmt = $pdo->prepare("SELECT * FROM candidats WHERE id_election = ? AND c_status = 'approved'");
$stmt->execute([$id_election]);
$candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SAWTY - Bureau de Vote</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { royalPurple: '#4c1d95', justiceGreen: '#059669' } } } }
    </script>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

    <header class="bg-white border-b border-gray-100 h-16 flex items-center">
        <div class="max-w-3xl mx-auto px-4 w-full flex justify-between items-center">
            <span class="text-xl font-black text-royalPurple">SAWTY<span class="text-justiceGreen">.</span></span>
            <span class="text-xs font-bold text-gray-500 uppercase">Bureau de Vote Sécurisé</span>
        </div>
    </header>

    <main class="flex-grow max-w-3xl mx-auto w-full px-4 py-8">
        <h1 class="text-2xl font-black text-gray-900 mb-2"><?php echo htmlspecialchars($elec['e_title']); ?></h1>
        <p class="text-sm text-gray-500 mb-8">Veuillez consulter l'intégralité des candidatures ci-dessous pour activer votre droit de vote.</p>

        <form action="process_vote.php" method="POST" id="voteForm">
            <input type="hidden" name="id_election" value="<?php echo $id_election; ?>">
            
            <!-- Forced-View Candidate Container -->
            <div id="candidate-list" class="bg-white border border-gray-200 rounded-2xl p-6 h-80 overflow-y-auto shadow-inner mb-8 space-y-4">
                <?php foreach ($candidates as $c): ?>
                    <label class="flex items-center space-x-4 border-b border-gray-50 pb-4 cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition">
                        <input type="radio" name="id_candidat" value="<?php echo $c['id_candidat']; ?>" required class="w-5 h-5 text-justiceGreen focus:ring-justiceGreen">
                        <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center font-bold text-royalPurple">
                            <?php echo substr($c['full_name'], 0, 1); ?>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900"><?php echo htmlspecialchars($c['full_name']); ?></h4>
                            <p class="text-xs text-gray-400">Programme complet disponible.</p>
                        </div>
                    </label>
                <?php endforeach; ?>
            </div>

            <!-- Action Area (Disabled until scroll) -->
            <div id="voting-actions" class="opacity-30 pointer-events-none transition-opacity duration-500 flex gap-4">
                <button type="submit" class="flex-grow bg-justiceGreen text-white font-bold py-4 rounded-2xl shadow-lg">Voter pour ce choix</button>
                <a href="process_vote.php?action=abstain&id=<?php echo $id_election; ?>" class="flex-grow bg-gray-200 text-gray-700 font-bold py-4 rounded-2xl text-center">Abstention</a>
            </div>
        </form>
    </main>

    <script>
        const list = document.getElementById('candidate-list');
        const actions = document.getElementById('voting-actions');

        list.addEventListener('scroll', () => {
            if (list.scrollTop + list.clientHeight >= list.scrollHeight - 20) {
                actions.classList.remove('opacity-30', 'pointer-events-none');
                actions.classList.add('opacity-100');
            }
        });
    </script>
</body>
</html>