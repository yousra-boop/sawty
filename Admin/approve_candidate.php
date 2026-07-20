<?php
// approve_candidate.php
require_once("../auth/connexion.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_candidat'])) {
    $id = intval($_POST['id_candidat']);
    $status = ($_POST['action'] === 'approve') ? 'approved' : 'rejected';
    
    $stmt = $pdo->prepare("UPDATE Candidats SET c_status = ? WHERE id_candidat = ?");
    $stmt->execute([$status, $id]);
}

header("Location: pending_candidates.php");
exit();
?>