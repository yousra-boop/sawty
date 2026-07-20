<?php
session_start();
require_once("../auth/connexion.php");

// Ensure the user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    try {
        // Updated field name to 'admin_password' as per your database
        // Verify 'id_admin' matches your primary key column name
        $stmt = $pdo->prepare("UPDATE Admins SET admin_password = ?, is_temp = 0 WHERE id_admin = ?");
        $stmt->execute([$new_pass, $_SESSION['admin_id']]);
        
        $_SESSION['is_temp'] = 0; // Update session
        header("Location: admin_dashboard.php");
        exit();
    } catch (PDOException $e) {
        die("Erreur de mise à jour : " . $e->getMessage());
    }
}
?>
<form method="POST">
    <input type="password" name="password" placeholder="New Password" required>
    <button type="submit">Update Password & Enter Dashboard</button>
</form>