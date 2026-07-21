<?php
session_start();
require_once("../auth/connexion.php");

// 1. Guard Clause - Verify admin session
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$msg_success = "";
$msg_error = "";
$step = 1;
$selected_election = null;
$num_voters = 0;

// Fetch all elections for the dropdown selection
$stmt_elec = $pdo->query("SELECT id_election, e_title FROM Elections ORDER BY id_election DESC");
$elections = $stmt_elec->fetchAll(PDO::FETCH_ASSOC);

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // STEP 1: Admin submitted the election & total count -> Generate input rows
    if (isset($_POST['action']) && $_POST['action'] === 'generate_rows') {
        $selected_election = intval($_POST['id_election']);
        $num_voters = intval($_POST['num_voters']);

        if ($selected_election > 0 && $num_voters > 0) {
            $step = 2; // Move to row entry step
        } else {
            $msg_error = "Veuillez sélectionner une élection valide et entrer un nombre de votants supérieur à 0.";
        }
    }

    // STEP 2: Admin submitted the actual list of codes -> Bulk insert into ListesBlanches
    if (isset($_POST['action']) && $_POST['action'] === 'save_list') {
        $selected_election = intval($_POST['id_election']);
        $identifiers = $_POST['identifiant'] ?? [];

        if ($selected_election > 0 && !empty($identifiers)) {
            try {
                $pdo->beginTransaction();

                // Prepare insert statement matching your table structure
                $stmt = $pdo->prepare("INSERT INTO ListesBlanches (id_election, identifiant) VALUES (:id_election, :identifiant)");

                $inserted_count = 0;
                foreach ($identifiers as $code) {
                    $clean_code = trim($code);
                    if (!empty($clean_code)) {
                        // Insert each valid Code Massar / CIN
                        $stmt->execute([
                            'id_election' => $selected_election,
                            'identifiant' => $clean_code
                        ]);
                        $inserted_count++;
                    }
                }

                $pdo->commit();
                $msg_success = "$inserted_count votants ont été ajoutés avec succès à la liste blanche !";
                $step = 1; // Reset back to step 1
            } catch (Exception $e) {
                $pdo->rollBack();
                $msg_error = "Erreur lors de l'enregistrement de la liste : " . $e->getMessage();
                $step = 2; // Keep them on step 2 to retry
            }
        } else {
            $msg_error = "Données invalides. Veuillez réessayer.";
            $step = 1;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SAWTY - Gestion des Listes Blanches</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        sawty: {
                            purple: '#4c1d95',
                            purpleHover: '#3b0764',
                            green: '#10b981',
                            greenHover: '#059669'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800">

    <!-- Brand Header -->
    <header class="bg-white border-b border-gray-100 shadow-sm h-16">
        <div class="max-w-7xl mx-auto px-6 h-full flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <span class="text-xl font-black text-sawty-purple tracking-wider">SAWTY ADMIN.</span>
                <span class="w-2 h-2 rounded-full bg-sawty-green"></span>
            </div>
            <a href="admin_dashboard.php" class="text-xs font-bold text-gray-500 hover:text-sawty-purple transition">Retour au Dashboard</a>
        </div>
    </header>

    <!-- Main Workspace -->
    <main class="max-w-3xl mx-auto p-8">
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
            <h2 class="text-xl font-bold text-sawty-purple mb-1">Gestion des Listes Blanches</h2>
            <p class="text-xs text-gray-400 mb-6">Autorisez les étudiants éligibles par Code Massar ou CIN pour une élection spécifique.</p>

            <!-- Alert Notifications -->
            <?php if (!empty($msg_success)): ?>
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-xs font-semibold mb-6">
                    ✓ <?php echo $msg_success; ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($msg_error)): ?>
                <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl text-xs font-semibold mb-6">
                    ⚠ <?php echo $msg_error; ?>
                </div>
            <?php endif; ?>

            <!-- STEP 1: Select Election and Count -->
            <?php if ($step === 1): ?>
                <form action="" method="POST" class="space-y-5">
                    <input type="hidden" name="action" value="generate_rows">
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Sélectionner l'élection</label>
                        <select name="id_election" required class="w-full border border-gray-200 px-4 py-3 rounded-xl text-xs focus:outline-none focus:border-sawty-purple">
                            <option value="">-- Choisir une élection --</option>
                            <?php foreach ($elections as $elec): ?>
                                <option value="<?php echo $elec['id_election']; ?>"><?php echo htmlspecialchars($elec['e_title']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nombre total de votants à ajouter</label>
                        <input type="number" name="num_voters" min="1" max="500" value="5" required class="w-full border border-gray-200 px-4 py-3 rounded-xl text-xs focus:outline-none focus:border-sawty-purple">
                        <p class="text-[10px] text-gray-400 mt-1">Ceci générera un formulaire dynamique pour remplir les identifiants un par un.</p>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit" class="bg-sawty-purple text-white px-6 py-3 rounded-xl text-xs font-bold hover:bg-sawty-purpleHover transition shadow-sm">
                            Générer le formulaire
                        </button>
                    </div>
                </form>

            <!-- STEP 2: Fill Identifiers Vector -->
            <?php elseif ($step === 2): ?>
                <form action="" method="POST" class="space-y-5">
                    <input type="hidden" name="action" value="save_list">
                    <input type="hidden" name="id_election" value="<?php echo $selected_election; ?>">

                    <div class="bg-purple-50 p-4 rounded-xl mb-4 text-xs text-sawty-purple font-medium">
                        Saisie des identifiants (Code Massar ou CIN) pour l'élection sélectionnée. Total : <strong><?php echo $num_voters; ?></strong> entrées.
                    </div>

                    <div class="space-y-3 max-h-96 overflow-y-auto pr-2">
                        <?php for ($i = 0; $i < $num_voters; $i++): ?>
                            <div class="flex items-center space-x-3">
                                <span class="text-xs font-mono text-gray-400 w-6 text-right"><?php echo ($i + 1); ?>.</span>
                                <input type="text" name="identifiant[]" placeholder="Entrer le Code Massar ou CIN" required class="w-full border border-gray-200 px-4 py-2.5 rounded-xl text-xs focus:outline-none focus:border-sawty-purple">
                            </div>
                        <?php endfor; ?>
                    </div>

                    <div class="pt-4 flex justify-between">
                        <a href="listes_blanches.php" class="px-5 py-3 rounded-xl text-xs font-bold text-gray-500 hover:bg-gray-100 transition">Annuler</a>
                        <button type="submit" class="bg-sawty-green text-white px-6 py-3 rounded-xl text-xs font-bold hover:bg-sawty-greenHover transition shadow-sm">
                            Enregistrer la liste blanche
                        </button>
                    </div>
                </form>
            <?php endif; ?>

        </div>
    </main>
</body>
</html>