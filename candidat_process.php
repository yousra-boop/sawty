<?php
session_start();
require_once("auth/connexion.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_user = $_SESSION['user_id'];
    $id_election = $_POST['id_election'];
    $c_bio = $_POST['c_bio'];

    // Simple file upload logic
    $upload_dir = "uploads/";
    $photo_path = $upload_dir . time() . "_" . basename($_FILES['c_photo']['name']);
    move_uploaded_file($_FILES['c_photo']['tmp_name'], $photo_path);

    // Insert into DB
    $sql = "INSERT INTO Candidats (id_user, id_election, c_bio, c_photo, c_status) VALUES (?, ?, ?, ?, 'pending')";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_user, $id_election, $c_bio, $photo_path]);

    header("Location: dashboard.php?success=candidature_sent");
}
?>