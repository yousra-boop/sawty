<?php
session_start();
require_once("auth/connexion.php");

if (isset($_GET['id_check'])) {
    $code_massar = $_GET['id_check'];

    // Querying the ListesBlanches table as per your schema
   $sql = "SELECT e.* FROM Elections e 
        JOIN ListesBlanches lb ON e.id_election = lb.id_election 
        WHERE lb.identifiant = :code";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':code', $code_massar);
    $stmt->execute();
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Save the results in the session to show on the dashboard
    $_SESSION['eligible_elections'] = $results;
    
    // Redirect back to dashboard
    header("Location: dashboard.php?id_check=" . urlencode($code_massar));
    exit();
}
?>

