<?php
session_start();
require_once("auth/connexion.php");

// 1. Basic Auth Guard
if (!isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit();
}

// 2. Fetch Election & Candidates
$id_election = $_GET['id'] ?? 0;
$election = $pdo->prepare("SELECT * FROM Elections WHERE id_election = ?");
$election->execute([$id_election]);
$elec = $election->fetch(PDO::FETCH_ASSOC);


$stmt = $pdo->prepare("
    SELECT c.*, u.user_name, u.user_surname 
    FROM candidats c
    JOIN users u ON c.id_user = u.id_user 
    WHERE c.id_election = ? AND c.c_status = 'approved'
");
$stmt->execute([$id_election]);
$candidats = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <p class="text-sm text-gray-500 mb-8">Veuillez consulter l'intégralité des candidatures ci-dessous pour valider votre droit de vote.</p>

    <!-- FORM STARTS HERE -->
    <!-- ... inside your main tag ... -->
<form action="process_vote.php" method="POST" id="vote-form">
    <input type="hidden" name="id_election" value="<?php echo $id_election; ?>">
    <div id="candidate-list" class="bg-white border border-gray-200 rounded-2xl p-6 h-80 overflow-y-auto shadow-inner mb-8 space-y-4">
        
        <?php foreach ($candidats as $c): ?>
            <label class="flex items-center space-x-4 border-b border-gray-50 pb-4 cursor-pointer hover:bg-gray-50 p-2 rounded-lg">
                <input type="radio" name="id_candidat" value="<?php echo $c['id_candidat']; ?>" required class="w-5 h-5 text-justiceGreen">
                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center font-bold text-royalPurple">
                    <?php echo substr($c['user_surname'], 0, 1); ?>
                </div>
                <h4 class="font-bold text-gray-900"><?php echo htmlspecialchars($c['user_name'] . ' ' . $c['user_surname']); ?></h4>
            </label>
        <?php endforeach; ?> <!-- This must be here to close the loop -->

    </div>
</form>
    <!-- FORM ENDS HERE -->

<!-- Remove 'pointer-events-none' from here -->
<div id="voting-actions" class="opacity-30 transition-opacity duration-500 flex gap-4">        <button type="submit" form="vote-form" class="flex-grow bg-justiceGreen text-white font-bold py-4 rounded-2xl">
            Voter pour ce choix
        </button>
        <form action="process_vote.php" method="POST" class="flex-grow">
            <input type="hidden" name="vote" value="abstain">
            <input type="hidden" name="id_election" value="<?php echo $id_election; ?>">
            <button type="submit" class="w-full bg-gray-200 text-gray-700 font-bold py-4 rounded-2xl">Abstention</button>
        </form>
    </div>
</main>

    <script>
        const list = document.getElementById('candidate-list');
        const actions = document.getElementById('voting-actions');

        list.addEventListener('scroll', () => {
            // Check if user reached 95% of the scrollable area
            if (list.scrollTop + list.clientHeight >= list.scrollHeight - 20) {
                actions.classList.remove('opacity-30', 'pointer-events-none');
                actions.classList.add('opacity-100');
            }
        });
    </script>
</body>
</html>