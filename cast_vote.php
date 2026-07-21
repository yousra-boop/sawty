<?php
session_start();
require_once("auth/connexion.php");

// Guard Clause: Check if user is logged in
if (!isset($_SESSION['user_name'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_election = $_POST['id_election'] ?? null;
    $selected_candidate = $_POST['id_candidat'] ?? null;
    $user_protest = isset($_POST['user_protest']) ? 1 : 0;
    $protest_reason = trim($_POST['protest_reason'] ?? '');
    
    $user_id = $_SESSION['user_id'] ?? null;
    $national_id = $_SESSION['verified_code'] ?? 'ANONYMOUS_CODE';

    if (!$id_election) {
        header("Location: dashboard.php");
        exit();
    }

    try {
        $pdo->beginTransaction();

        // 1. Check if the user has already voted in this election
        $check_log = $pdo->prepare("SELECT id_vote FROM votes_logs WHERE id_election = ? AND national_id = ?");
        $check_log->execute([$id_election, $national_id]);
        if ($check_log->fetch()) {
            $pdo->rollBack();
            $_SESSION['vote_flash_error'] = "Erreur de sécurité : Vous avez déjà voté pour ce scrutin.";
            header("Location: dashboard.php");
            exit();
        }

        // 2. Resolve candidate name to id_candidat
        $id_candidat = null;
        if (!empty($selected_candidate) && !$user_protest) {
            $name_parts = explode(' ', $selected_candidate);
            $firstName = $name_parts[0] ?? '';
            $lastName = $name_parts[1] ?? '';

            $cand_query = $pdo->prepare("
                SELECT c.id_candidat 
                FROM Candidats c 
                JOIN users u ON c.id_user = u.id_user 
                WHERE c.id_election = ? AND u.user_name = ? AND u.user_surname = ?
            ");
            $cand_query->execute([$id_election, $firstName, $lastName]);
            $cand_res = $cand_query->fetch(PDO::FETCH_ASSOC);
            
            if ($cand_res) {
                $id_candidat = $cand_res['id_candidat'];
            }
        }

        // 3. Insert into `enveloppes` (Completely Anonymous)
        $env_stmt = $pdo->prepare("
            INSERT INTO envloppes (id_election, id_candidat, voted_at, user_protest, protest_reason) 
            VALUES (?, ?, NOW(), ?, ?)
        ");
        $env_stmt->execute([$id_election, $id_candidat, $user_protest, $protest_reason]);

        // 4. Insert into `votes_logs`
        $log_stmt = $pdo->prepare("
            INSERT INTO votes_logs (id_election, vote_token, voted_at, national_id) 
            VALUES (?, ?, NOW(), ?)
        ");
        $vote_token = bin2hex(random_bytes(16));
        $log_stmt->execute([$id_election, $vote_token, $national_id]);

        $pdo->commit();

        $_SESSION['vote_flash_success'] = "Votre vote a été enregistré avec succès et de manière totalement anonyme !";
        header("Location: dashboard.php");
        exit();

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        die("Erreur technique lors de l'enregistrement du vote : " . htmlspecialchars($e->getMessage()));
    }
} else {
    header("Location: dashboard.php");
    exit();
}
?>