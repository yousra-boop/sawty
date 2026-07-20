<?php
// var_dump($_POST); exit;
session_start();
require_once("../auth/connexion.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $login = trim($_POST['admin_login']); // Matches the name="admin_login" in form
    $password = $_POST['password'];

    if (!empty($login) && !empty($password)) {
        try {
            // Changed to filter by admin_login
            $stmt = $pdo->prepare("SELECT * FROM Admins WHERE admin_login = :login LIMIT 1");
            $stmt->execute(['login' => $login]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$admin) {
    die("Debug: User not found in database.");
}
if (!password_verify($password, $admin['admin_password'])) {
    die("Debug: Password verification failed. The hash in the DB does not match the password.");
}

           if ($admin && password_verify($password, $admin['admin_password'])) {
                session_regenerate_id(true);
                
                // Set the session variables
                $_SESSION['admin_id'] = $admin['id_admin'];
                $_SESSION['is_temp'] = $admin['is_temp']; // <-- ADD THIS LINE
                
                header("Location: admin_dashboard.php");
                exit();
            } else {
                header("Location: admin_login.php?error=invalid");
                exit();
            }
        } catch (PDOException $e) {
            header("Location: admin_login.php?error=server");
            exit();
        }
    }
}
?>