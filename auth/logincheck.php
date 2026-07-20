<?php
// 1. Connexion à la base de données
require_once("connexion.php");
session_start();

// 2. On vérifie si les champs ont été envoyés via POST (le "isset" de ton cours)
if (isset($_POST['user_email']) && isset($_POST['user_password'])) {

    // 3. Récupération des informations du compte
    $email = $_POST['user_email'];
    $pass_saisi = $_POST['user_password'];

    // 4. Préparation de la requête pour chercher l'utilisateur par son email
    $sql = "SELECT * FROM Users WHERE user_email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':email', $email);
    $stmt->execute();
    
    // On récupère le résultat
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    

    // 5. Vérification du mot de passe (haché)
    if ($user && password_verify($pass_saisi, $user['user_password'])) {
        // Succès : on crée les variables de session
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['user_name'] = $user['user_name'];
        
        // Redirection vers le tableau de bord
        header("Location: ../dashboard.php");
        exit();
    } else {
        // Échec : redirection vers index.php avec un message d'erreur dans l'URL
        header("Location: ../index.php?error=1");
        exit();
    }
} else {
    // Si quelqu'un accède à la page sans soumettre le formulaire, on le renvoie à l'accueil
    header("Location: ../index.php");
    exit();
}
?>