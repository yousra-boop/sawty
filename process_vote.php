<?php
session_start();
require_once("auth/connexion.php");

// 1. Verify User Access
if (!isset($_SESSION['user_id'])) {
    die("Accès non autorisé. Veuillez vous connecter.");
}

$user_id = $_SESSION['user_id'];
$id_election = $_POST['id_election'] ?? 0;

// Fetch national_id for logs
$stmt = $pdo->prepare("SELECT national_id FROM users WHERE id_user = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$national_id = $user['national_id'];

try {
    // 2. INTEGRITY CHECK: Verify if the user has already voted
    $check = $pdo->prepare("SELECT COUNT(*) FROM votes_logs WHERE id_election = ? AND national_id = ?");
    $check->execute([$id_election, $national_id]);
    
    if ($check->fetchColumn() > 0) {
        die("Erreur : Vous avez déjà voté pour cette élection.");
    }

    // 3. Logic for Abstention
    if (isset($_POST['vote']) && $_POST['vote'] == 'abstain') {
        $log = $pdo->prepare("INSERT INTO votes_logs (id_election, national_id, voted_at) VALUES (?, ?, NOW())");
        $log->execute([$id_election, $national_id]);
        header("Location: voter_dashboard.php?id=$id_election&msg=abstention_enregistree");
        exit();
    }

    // 4. Logic for Casting a Vote
    $id_candidat = $_POST['id_candidat'] ?? null;
    if (!$id_candidat) die("Aucun candidat sélectionné.");

    $pdo->beginTransaction();
    
    // Log participation
    $vote_token = bin2hex(random_bytes(16)); 
    $log = $pdo->prepare("INSERT INTO votes_logs (id_election, national_id, voted_at, vote_token) VALUES (?, ?, NOW(), ?)");
    $log->execute([$id_election, $national_id, $vote_token]);
    
    // Cast ballot (Table name corrected to 'enveloppes')
    $vote = $pdo->prepare("INSERT INTO envloppes (id_election, id_candidat, voted_at) VALUES (?, ?, NOW())");
    $vote->execute([$id_election, $id_candidat]);
    
    $pdo->commit();
    header("Location: voter_dashboard.php?id=$id_election&msg=vote_enregistre");
    exit();

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    die("Erreur lors du vote: " . $e->getMessage());
}
?>