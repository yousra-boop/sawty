<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SAWTY - Admin Login</title>
    <link rel="stylesheet" href="../style/admin_login.css">
</head>
<body class="admin-body">
    <div class="login-container">
        <h2>Accès Administrateur <span class="logo">SAWTY</span></h2>
        <p>Veuillez vous authentifier pour accéder au panneau de gestion.</p>

       <form action="admin_login_check.php" method="POST">
    <div class="form-group">
        <label for="admin_login">Identifiant (Login)</label>
        <input type="text" id="admin_login" name="admin_login" required>
        
    </div>
    <div class="form-group">
        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" required>
    </div>
    <button type="submit" class="submit-btn">Se connecter</button>
</form>
    </div>
</body>
</html>