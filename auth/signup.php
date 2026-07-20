

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un compte - Sawty</title>
    <link rel="stylesheet" href="style/signup.css">
</head>
<body class="signup-page">

<main class="signup-layout">
    <!-- Partie Gauche : Formulaire -->
    <section class="signup-form-side">
        <h2>Créer votre compte</h2>
        <form action="signupinsert.php" method="POST" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group"><label>Prénom</label><input type="text" name="user_name" required></div>
                <div class="form-group"><label>Nom</label><input type="text" name="user_surname" required></div>
            </div>
            <label>Email Étudiant</label><input type="email" name="user_email" required>
            <label>Téléphone</label><input type="text" name="user_phone" required>
            <label>National ID</label><input type="text" name="national_id" required>
            <label>Mot de passe</label><input type="password" name="user_password" required>
            <label>Photo de profil</label>
    <input type="file" name="user_avatar" accept="image/*" required>
            <button type="submit" class="btn-submit">S'inscrire</button>
        </form>
    </section>

    <!-- Partie Droite : Engagements -->
    <section class="signup-info-side">
        <div class="logo-large">SAWTY.</div>
        <h3>L'engagement Sawty</h3>
        <p>En créant ce compte, vous rejoignez une plateforme dédiée à la transparence électorale.</p>
        <ul class="pledge-list">
            <li> Intégrité totale des données.</li>
            <li> Confidentialité absolue du vote.</li>
            <li> Vérification sécurisée.</li>
        </ul>
    </section>
</main>

</body>
</html>
