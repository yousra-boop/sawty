<?php
session_start();
require_once("../auth/connexion.php");

// Fetch pending candidates
$stmt = $pdo->query("SELECT c.*, u.user_name, u.user_surname 
                     FROM Candidats c 
                     JOIN users u ON c.id_user = u.id_user 
                     WHERE c.c_status = 'pending'");
$pending_candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Candidatures en Attente</title>
    <!-- Ensure this path is correct! -->
    <link rel="stylesheet" href="../style/pending_candidates.css"> 
</head>
<body class="bg-gray-50 p-8">
    <h2 class="text-2xl font-bold mb-6">Candidatures en Attente</h2>
    
    <?php foreach ($pending_candidates as $cand): ?>
        <div class="cand-card">
            <div class="cand-info">
                <h4><?php echo htmlspecialchars($cand['user_name'] . ' ' . $cand['user_surname']); ?></h4>
                <p><strong>Bio:</strong> <?php echo htmlspecialchars($cand['c_bio']); ?></p>
                <!-- Fix image path: Add '../' to access the root uploads folder -->
                <img src="../<?php echo htmlspecialchars($cand['c_photo']); ?>" width="100" alt="Profile">
            </div>
            <div class="actions">
                <form action="approve_candidate.php" method="POST">
                    <input type="hidden" name="id_candidat" value="<?php echo $cand['id_candidat']; ?>">
                    <button name="action" value="approve" class="btn-approve">Approuver</button>
                    <button name="action" value="reject" class="btn-reject">Rejeter</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
</body>
</html>