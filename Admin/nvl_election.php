<?php
session_start();
require_once("../auth/connexion.php");

// 1. Guard Clause - Verify admin session
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$msg_error = '';
$election = [
    'e_title' => '',
    'e_description' => '',
    'start_date' => '',
    'end_date' => '',
    'status' => 'pending'
];

$is_edit = false;
$id_election = null;

// 2. Check if we are in EDIT mode (if an ID is passed in the URL)
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $is_edit = true;
    $id_election = intval($_GET['id']);

    // Fetch existing election details
    $stmt = $pdo->prepare("SELECT * FROM Elections WHERE id_election = :id LIMIT 1");
    $stmt->execute(['id' => $id_election]);
    $election = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$election) {
        header("Location: admin_dashboard.php");
        exit();
    }
}

// 3. Handle Form Submission (Both Add and Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['e_title'] ?? '');
    $description = trim($_POST['e_description'] ?? '');
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $status = $_POST['status'] ?? 'pending';
    $admin_id = $_SESSION['admin_id'];

    $posterName = $election['poster'] ?? null; // Keep old poster by default if editing

    // Handle Poster Upload safely
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['poster']['tmp_name'];
        $fileName = $_FILES['poster']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadFileDir = "../uploads/";
            
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            
            $dest_path = $uploadFileDir . $newFileName;
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $posterName = $newFileName;
            } else {
                $msg_error = "Erreur lors du déplacement du fichier téléchargé.";
            }
        } else {
            $msg_error = "Format d'image non autorisé (Seulement JPG, PNG, WEBP).";
        }
    }

    if (empty($msg_error) && !empty($title) && !empty($start_date) && !empty($end_date)) {
        if ($is_edit) {
            // UPDATE Query
            $stmt = $pdo->prepare("UPDATE Elections SET e_title = :title, e_description = :description, start_date = :start_date, end_date = :end_date, status = :status, poster = :poster WHERE id_election = :id");
            $success = $stmt->execute([
                'title' => $title,
                'description' => $description,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'status' => $status,
                'poster' => $posterName,
                'id' => $id_election
            ]);
            $redirect_param = "election_updated";
        } else {
            // INSERT Query
            $stmt = $pdo->prepare("INSERT INTO Elections (e_title, e_description, start_date, end_date, status, poster, id_admin) VALUES (:title, :description, :start_date, :end_date, :status, :poster, :admin_id)");
            $success = $stmt->execute([
                'title' => $title,
                'description' => $description,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'status' => $status,
                'poster' => $posterName,
                'admin_id' => $admin_id
            ]);
            $redirect_param = "election_added";
        }

        if ($success) {
            header("Location: admin_dashboard.php?success=" . $redirect_param);
            exit();
        } else {
            $msg_error = "Erreur lors de l'enregistrement dans la base de données.";
        }
    } elseif (empty($msg_error)) {
        $msg_error = "Veuillez remplir tous les champs obligatoires.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SAWTY - <?= $is_edit ? "Modifier" : "Ajouter" ?> une Élection</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        sawty: {
                            purple: '#4c1d95',
                            purpleHover: '#3b0764',
                            green: '#10b981',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800">

    <header class="bg-white border-b border-gray-100 shadow-sm h-16">
        <div class="max-w-7xl mx-auto px-6 h-full flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <span class="text-xl font-black text-sawty-purple tracking-wider">SAWTY ADMIN.</span>
                <span class="w-2 h-2 rounded-full bg-sawty-green"></span>
            </div>
            <a href="admin_dashboard.php" class="text-xs font-bold text-gray-500 hover:text-sawty-purple transition">Retour au Dashboard</a>
        </div>
    </header>

    <main class="max-w-3xl mx-auto p-8">
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
            <h2 class="text-xl font-bold text-sawty-purple mb-1"><?= $is_edit ? "Modifier l'élection" : "Créer une nouvelle élection" ?></h2>
            <p class="text-xs text-gray-400 mb-6">Remplissez les détails ci-dessous et gérez l'affiche officielle.</p>

            <?php if (!empty($msg_error)): ?>
                <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl text-xs mb-6 font-semibold">
                    ⚠ <?= htmlspecialchars($msg_error) ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data" class="space-y-5">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Titre de l'élection</label>
                    <input type="text" name="e_title" value="<?= htmlspecialchars($election['e_title']) ?>" required class="w-full border border-gray-200 px-4 py-3 rounded-xl text-xs focus:outline-none focus:border-sawty-purple">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Description</label>
                    <textarea name="e_description" rows="3" class="w-full border border-gray-200 px-4 py-3 rounded-xl text-xs focus:outline-none focus:border-sawty-purple"><?= htmlspecialchars($election['e_description']) ?></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Date de début</label>
                        <input type="datetime-local" name="start_date" value="<?= date('Y-m-d\TH:i', strtotime($election['start_date'])) ?>" required class="w-full border border-gray-200 px-4 py-3 rounded-xl text-xs focus:outline-none focus:border-sawty-purple">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Date de fin</label>
                        <input type="datetime-local" name="end_date" value="<?= date('Y-m-d\TH:i', strtotime($election['end_date'])) ?>" required class="w-full border border-gray-200 px-4 py-3 rounded-xl text-xs focus:outline-none focus:border-sawty-purple">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Statut</label>
                    <select name="status" class="w-full border border-gray-200 px-4 py-3 rounded-xl text-xs focus:outline-none focus:border-sawty-purple">
                        <option value="pending" <?= $election['status'] === 'pending' ? 'selected' : '' ?>>En attente (Pending)</option>
                        <option value="active" <?= $election['status'] === 'active' ? 'selected' : '' ?>>Active (Ouverte)</option>
                        <option value="closed" <?= $election['status'] === 'closed' ? 'selected' : '' ?>>Fermée</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Affiche (Poster)</label>
                    <input type="file" name="poster" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-purple-50 file:text-sawty-purple hover:file:bg-purple-100">
                    <?php if (!empty($election['poster'])): ?>
                        <p class="text-[10px] text-gray-400 mt-1">Affiche actuelle enregistrée.</p>
                    <?php endif; ?>
                </div>

                <div class="pt-4 flex justify-end space-x-3">
                    <a href="admin_dashboard.php" class="px-5 py-3 rounded-xl text-xs font-bold text-gray-500 hover:bg-gray-100 transition">Annuler</a>
                    <button type="submit" class="bg-sawty-green text-white px-6 py-3 rounded-xl text-xs font-bold hover:opacity-90 transition shadow-sm">
                        <?= $is_edit ? "Mettre à jour" : "Créer l'élection" ?>
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>