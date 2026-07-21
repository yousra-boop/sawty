<?php
session_start();
require_once("auth/connexion.php");

// Guard clause
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_user = $_SESSION['user_id'];
    $id_election = $_POST['id_election'];
    $c_bio = $_POST['c_bio'];

    $upload_dir = "uploads/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Handle photo upload safely
    $photo_filename = "";
    if (isset($_FILES['c_photo']) && $_FILES['c_photo']['error'] === UPLOAD_ERR_OK) {
        $photo_filename = time() . "_photo_" . basename($_FILES['c_photo']['name']);
        move_uploaded_file($_FILES['c_photo']['tmp_name'], $upload_dir . $photo_filename);
    }

    // Handle video upload safely
    $video_filename = "";
    if (isset($_FILES['c_video']) && $_FILES['c_video']['error'] === UPLOAD_ERR_OK) {
        $video_filename = time() . "_video_" . basename($_FILES['c_video']['name']);
        move_uploaded_file($_FILES['c_video']['tmp_name'], $upload_dir . $video_filename);
    }

    // Insert into DB with all 5 parameters matching the placeholders
    $sql = "INSERT INTO candidats (id_user, id_election, c_bio, c_photo, c_video, c_status) VALUES (?, ?, ?, ?, ?, 'pending')";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_user, $id_election, $c_bio, $photo_filename, $video_filename]);

    // Redirect to the candidate portal with the submission success flag
    header("Location: candidat_portal.php?status=submitted");
    exit();
}
?>