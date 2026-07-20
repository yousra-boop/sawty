<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Sawty - Connexion</title>
    <link rel="stylesheet" href="style/index.css">
</head>
<body>

    <!-- Header avec Logo -->
   <header>
        <div class="logo">SAWTY.</div>
        <nav>Comment ça marche ?</nav>
    </header>

    <main class="container">
        <!-- Partie Gauche -->
 <section class="intro">
    <div class="badge">● Transparence & Intégrité Totale</div>
    
    <h1>Chaque vote est une enveloppe pour <span>faire entendre votre voix.</span></h1>
    
    <p class="main-desc">Bienvenue sur Sawty, le système d'élections sécurisé qui garantit à la fois la confidentialité absolue de votre choix et la vérification stricte de votre participation.</p>
    
    <hr>
    
    <div class="info-block">
        <h3>Anonymat Garanti</h3>
        <p>Le contenu de votre enveloppe ne contient aucun lien avec votre identité.</p>
    </div>
    
    <div class="info-block">
        <h3>Droit de Protestation</h3>
        <p>Exprimez officiellement votre désaccord via le vote blanc constructif.</p>
    </div>
</section>

        <!-- Formulaire de connexion -->
        <section class="login-box">
            <h2>Espace Électoral</h2>
            <p>Connectez-vous pour voter</p>
            
            <form action="auth/logincheck.php" method="POST">
                <label>Adresse Email Étudiante</label>
                <input type="email" name="user_email" required>
                
                <label>Mot de passe</label>
                <input type="password" name="user_password" required>
                
                <button type="submit">Accéder au bureau de vote</button>
            </form>
            
            <p class="signup-link">Nouveau sur la plateforme ? <a href="signup.php">Créer un compte</a></p>
        </section>
    </main>

</body>
</html>