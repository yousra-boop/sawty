<?php
session_start();
require_once("../auth/connexion.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $login = trim($_POST['admin_login']);
    $password = $_POST['password'];

    if (!empty($login) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM Admins WHERE admin_login = :login LIMIT 1");
            $stmt->execute(['login' => $login]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$admin) {
                die("DEBUG ERROR: User with login '<strong>" . htmlspecialchars($login) . "</strong>' was not found in the database!");
            }

            if (password_verify($password, $admin['admin_password'])) {
                session_regenerate_id(true);
                
                // Set the session variables
                $_SESSION['admin_id'] = $admin['id_admin'];
                $_SESSION['is_temp'] = $admin['is_temp']; 
                
                // FORK IN THE ROAD: Check if it's a temporary password
                if ($admin['is_temp'] == 1) {
                    header("Location: change_password.php");
                    exit();
                } else {
                    header("Location: admin_dashboard.php");
                    exit();
                }

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