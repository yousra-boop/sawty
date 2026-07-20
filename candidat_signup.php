<?php
 // This must be at the very top!
require_once("auth/connexion.php");
// Debugging: If this prints nothing, the session is lost
session_start();
if (!isset($_SESSION['user_id'])) { 
    header("Location: index.php");
    exit();
}
// Fetch only active elections for the dropdown
$stmt = $pdo->query("SELECT id_election, e_title FROM Elections WHERE status = 'Actif'");
$elections = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SAWTY - Devenir Candidat</title>
        <link rel="stylesheet" href="style/candidat_signup.css">
    
</head>
<body class="bg-gray-50 p-6">
    <div class="signup-container">
        <h2 class="form-title">Devenir Candidat</h2>
        
        <form action="candidat_process.php" method="POST" enctype="multipart/form-data">
            
            <div class="form-group">
                <label>Choisir le scrutin</label>
                <select name="id_election" required>
                    <?php foreach ($elections as $e): ?>
                        <option value="<?php echo $e['id_election']; ?>"><?php echo htmlspecialchars($e['e_title']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Biographie de campagne</label>
                <textarea name="c_bio" rows="4" required></textarea>
            </div>

            <div class="file-grid">
                <div class="form-group">
                    <label>Photo de profil</label>
                    <input type="file" name="c_photo" accept="image/*">
                </div>
                <div class="form-group">
                    <label>Vidéo de campagne</label>
                    <input type="file" name="c_video" accept="video/*">
                </div>
            </div>

            <div class="checkbox-group">
                <input type="checkbox" id="terms" name="terms" required>
                <label for="terms">En soumettant cette candidature, je m'engage à fournir des informations véridiques. Je comprends que ma candidature sera minutieusement examinée.</label>
            </div>

            <button type="submit" class="submit-btn">Soumettre ma candidature</button>
        </form>
    </div>
</body>
</html>