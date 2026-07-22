<?php
session_start();
require_once("auth/connexion.php");

$msg_error = "";
$candidate_info = null;

// Check if redirected right after submitting a new application
$just_submitted = isset($_GET['status']) && $_GET['status'] === 'submitted';

// Handle Candidate Portal Login / Status Check
if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['identifiant'])) {
    $identifier = trim($_POST['identifiant'] ?? $_GET['identifiant'] ?? '');

    if (!empty($identifier)) {
        // Updated to use your exact table name 'Candidats' (with capital C)
        $sql = "SELECT c.*, e.e_title FROM Candidats c 
                JOIN Elections e ON c.id_election = e.id_election 
                JOIN users u ON c.id_user = u.id_user 
                JOIN ListesBlanches lb ON lb.id_election = c.id_election AND lb.identifiant = :identifiant
                WHERE u.national_id = :identifiant OR lb.identifiant = :identifiant
                ORDER BY c.id_candidat DESC LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['identifiant' => $identifier]);
        $candidate_info = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$candidate_info) {
            $msg_error = "Aucune candidature trouvée pour cet identifiant. Veuillez d'abord soumettre votre candidature.";
        }
    } else {
        $msg_error = "Veuillez entrer votre Code Massar ou CIN.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Sawty - Portail Candidat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style/dashboard.css">
</head>
<body class="bg-gray-50 text-gray-800">

    <!-- Header -->
    <header class="bg-white border-b border-gray-100 shadow-sm h-16">
        <div class="max-w-7xl mx-auto px-4 h-full flex items-center justify-between">
            <h1 class="text-xl font-black text-purple-900">SAWTY.</h1>
            <nav class="space-x-6 text-sm font-bold">
                <a href="dashboard.php" class="text-gray-600 hover:text-purple-900 transition">Retour au Dashboard</a>
            </nav>
        </div>
    </header>

    <!-- Main Section -->
    <main class="max-w-xl mx-auto p-8 mt-10">
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
            
            <h2 class="text-xl font-bold text-purple-900 mb-1">Portail de Suivi Candidat</h2>
            <p class="text-xs text-gray-400 mb-6">Consultez l'état d'avancement de votre candidature (En attente, Approuvé ou Rejeté).</p>

            <!-- Success Notification after Submitting -->
            <?php if ($just_submitted): ?>
                <div class="bg-purple-50 border border-purple-200 text-purple-900 px-4 py-3 rounded-xl text-xs font-semibold mb-6">
🎉 Merci pour votre intérêt ! Votre candidature a été soumise avec succès et est actuellement en cours d'examen par l'administration.                </div>
            <?php endif; ?>

            <!-- Error Notification -->
            <?php if (!empty($msg_error)): ?>
                <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl text-xs font-semibold mb-6">
                    ⚠ <?php echo $msg_error; ?>
                </div>
            <?php endif; ?>

            <!-- LOGIN FORM (Shown if not searched yet or invalid) -->
            <?php if (!$candidate_info): ?>
                <form action="" method="POST" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Code Massar ou CIN</label>
                        <input type="text" name="identifiant" required class="w-full border border-gray-200 px-4 py-3 rounded-xl text-xs focus:outline-none focus:border-purple-900" placeholder="Entrez votre identifiant">
                    </div>
                    <button type="submit" class="w-full bg-purple-900 text-white font-bold text-xs py-3 rounded-xl hover:bg-purple-800 transition shadow-sm">
                        Vérifier mon statut
                    </button>
                </form>
            <?php else: ?>
                <!-- STATUS RESULT VIEW -->
                <div class="space-y-6">
                    <div class="bg-purple-50 p-4 rounded-xl text-xs text-purple-900 space-y-1">
                        <div><strong>Élection :</strong> <?php echo htmlspecialchars($candidate_info['e_title']); ?></div>
                    </div>

                    <!-- Dynamic Status Alert Box -->
                    <div class="p-6 rounded-2xl text-center border shadow-sm 
                        <?php 
                            if ($candidate_info['c_status'] === 'approved') echo 'bg-emerald-50 border-emerald-200 text-emerald-800';
                            elseif ($candidate_info['c_status'] === 'rejected') echo 'bg-rose-50 border-rose-200 text-rose-800';
                            else echo 'bg-amber-50 border-amber-200 text-amber-800';
                        ?>">
                        
                        <div class="text-sm font-black uppercase tracking-wider mb-2">
                            <?php 
                                if ($candidate_info['c_status'] === 'approved') echo '● Candidature Approuvée !';
                                elseif ($candidate_info['c_status'] === 'rejected') echo '■ Candidature Refusée';
                                else echo '⏳ En Attente de Révision';
                            ?>
                        </div>

                        <p class="text-xs leading-relaxed">
                            <?php 
                                if ($candidate_info['c_status'] === 'approved') {
                                    echo "Félicitations ! Votre candidature a été acceptée par l'administration. Vous pouvez lancer votre campagne électorale.";
                                } elseif ($candidate_info['c_status'] === 'rejected') {
                                    echo "Nous sommes désolés, votre dossier de candidature n'a pas été retenu pour cette session.";
                                } else {
                                    echo "Application pending please await for review. Votre dossier est en cours d'examen par les administrateurs.";
                                }
                            ?>
                        </p>
                    </div>

                    <div class="pt-2 flex justify-between">
                        <a href="dashboard.php" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold px-4 py-2 rounded-xl transition">Quitter</a>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </main>
</body>
</html>