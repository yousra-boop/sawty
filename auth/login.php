<?php
require_once("connexion.php");
session_start(); // Très important pour garder l'utilisateur connecté

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['user_email'];
    $password = $_POST['user_password'];

    // 1. On cherche l'utilisateur par son email
    $sql = "SELECT * FROM Users WHERE user_email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // 2. Si l'utilisateur existe
    if ($user) {
        // 3. On vérifie si le mot de passe saisi correspond au hash stocké
        if (password_verify($password, $user['user_password'])) {
            // Mot de passe correct ! On crée la session
          $_SESSION['user_id'] = $user['id_user']; // This pulls the ID from your DB row
            $_SESSION['user_name'] = $user['user_name'];
         
            header("Location: ..dashboard.php"); // Ou ta page d'accueil après connexion
            exit();
        } else {
            echo "Mot de passe incorrect.";
        }
    } else {
        echo "Aucun compte trouvé avec cet email.";
    }
}
?>