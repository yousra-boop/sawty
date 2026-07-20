<?php
// 1. Connexion à la base de données
require_once("connexion.php");

// 2. Vérifier si le formulaire a été envoyé via la méthode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 3. Récupérer les données envoyées par le formulaire
    $nom = $_POST['user_name'];
    $prenom = $_POST['user_surname'];
    $email = $_POST['user_email'];
    $phone = $_POST['user_phone'];
    $pass = $_POST['user_password'];
    $nid = $_POST['national_id'];

    // 4. Gestion de l'image
    // On s'assure que le dossier 'uploads' existe, sinon on le crée
    if (!is_dir('uploads')) { mkdir('uploads', 0777, true); }
    
    // On définit le chemin complet où l'image sera enregistrée
    $target_file = "uploads/" . basename($_FILES["user_avatar"]["name"]);
    
    // On déplace le fichier du dossier temporaire du serveur vers notre dossier 'uploads'
    move_uploaded_file($_FILES["user_avatar"]["tmp_name"], $target_file);
    
    // On garde le chemin du fichier pour l'enregistrer dans la base de données
    $avatar_path = $target_file;

    // 5. Préparer la requête SQL pour insérer les informations
    $sql = "INSERT INTO Users (user_name, user_surname, user_email, user_phone, user_password, national_id, user_avatar) 
            VALUES (:nom, :prenom, :email, :phone, :pass, :nid, :avatar)";
    
    // On prépare la requête pour éviter les injections SQL
    $stmt = $pdo->prepare($sql);

    // 6. Lier les paramètres de la requête aux variables PHP
    $stmt->bindValue(':nom', $nom);
    $stmt->bindValue(':prenom', $prenom);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':phone', $phone);
    $pass = password_hash($_POST['user_password'], PASSWORD_DEFAULT);
    $stmt->bindValue(':pass', $pass);
    $stmt->bindValue(':nid', $nid);
    $stmt->bindValue(':avatar', $avatar_path);

    // 7. Exécuter la requête et gérer les erreurs possibles
    try {
        $stmt->execute();
        // Redirection vers l'accueil si tout s'est bien passé
        header("Location: dashboard.php");
        exit();
    } catch (PDOException $e) {
        // Affiche l'erreur si l'email existe déjà ou autre problème de base de données
        echo "Erreur d'insertion : " . $e->getMessage();
    }
}
?>