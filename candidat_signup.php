<?php
session_start();
require_once("auth/connexion.php");

// Guard Clause - Verify user session
if (!isset($_SESSION['user_id'])) { 
    header("Location: index.php");
    exit();
}

$elections = [];
$msg_error = "";
$id_check = trim($_GET['id_check'] ?? '');

// If Code Massar / CIN is submitted, check qualification against ListesBlanches
if (!empty($id_check)) {
    $sql = "SELECT e.id_election, e.e_title 
            FROM Elections e 
            JOIN ListesBlanches lb ON e.id_election = lb.id_election 
            WHERE lb.identifiant = :code AND (e.status = 'active' OR e.status = 'Actif')";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':code' => $id_check]);
    $elections = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($elections)) {
        $msg_error = "Aucune élection active trouvée pour cet identifiant. Si vous pensez qu'il s'agit d'une erreur, contactez l'administration.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SAWTY - Devenir Candidat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style/candidat_signup.css">
</head>
<body class="bg-gray-50 p-6 min-h-screen flex items-center justify-center">
    <div class="signup-container max-w-xl w-full bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
        <h2 class="form-title text-xl font-black text-purple-900 mb-2">Devenir Candidat</h2>
        <p class="text-xs text-gray-400 mb-6">Vérifiez votre éligibilité pour afficher les scrutins auxquels vous pouvez postuler.</p>

        <!-- Display Error Notification if Not Eligible -->
        <?php if (!empty($msg_error)): ?>
            <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl text-xs font-semibold mb-6">
                ⚠ <?php echo $msg_error; ?>
            </div>
        <?php endif; ?>

        <!-- STEP 1: Verify Code Massar / CIN -->
        <form action="" method="GET" class="mb-6 space-y-3">
            <label class="block text-xs font-bold uppercase text-gray-500">1. Vérifier mon éligibilité</label>
            <div class="flex space-x-2">
                <input type="text" name="id_check" required value="<?php echo htmlspecialchars($id_check); ?>" 
                       placeholder="Entrer Code Massar ou CIN" 
                       class="w-full border border-gray-200 px-4 py-2.5 rounded-xl text-xs focus:outline-none focus:border-purple-900">
                <button type="submit" class="bg-purple-900 text-white font-bold text-xs px-5 py-2.5 rounded-xl hover:bg-purple-800 transition shadow-sm whitespace-nowrap">
                    Vérifier
                </button>
            </div>
        </form>

        <!-- STEP 2: Candidate Application Form (Only appears if eligible elections are found) -->
        <?php if (!empty($elections)): ?>
            <hr class="border-gray-100 my-6">

            <form action="candidat_process.php" method="POST" enctype="multipart/form-data" class="space-y-5">
                <input type="hidden" name="identifiant" value="<?php echo htmlspecialchars($id_check); ?>">

                <div class="form-group space-y-1">
                    <label class="block text-xs font-bold uppercase text-gray-500">2. Choisir le scrutin éligible</label>
                    <select name="id_election" required class="w-full border border-gray-200 px-4 py-2.5 rounded-xl text-xs focus:outline-none focus:border-purple-900">
                        <?php foreach ($elections as $e): ?>
                            <option value="<?php echo $e['id_election']; ?>"><?php echo htmlspecialchars($e['e_title']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group space-y-1">
                    <label class="block text-xs font-bold uppercase text-gray-500">Biographie de campagne</label>
                    <textarea name="c_bio" rows="4" required placeholder="Présentez votre programme et motivations..." class="w-full border border-gray-200 p-3 rounded-xl text-xs focus:outline-none focus:border-purple-900"></textarea>
                </div>

                <div class="file-grid grid grid-cols-2 gap-4">
                    <div class="form-group space-y-1">
                        <label class="block text-xs font-bold uppercase text-gray-500">Photo de profil</label>
                        <input type="file" name="c_photo" accept="image/*" class="text-xs text-gray-500 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-purple-50 file:text-purple-900 hover:file:bg-purple-100">
                    </div>
                    <div class="form-group space-y-1">
                        <label class="block text-xs font-bold uppercase text-gray-500">Vidéo de campagne</label>
                        <input type="file" name="c_video" accept="video/*" class="text-xs text-gray-500 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-purple-50 file:text-purple-900 hover:file:bg-purple-100">
                    </div>
                </div>

                <div class="checkbox-group flex items-start space-x-2 pt-2">
                    <input type="checkbox" id="terms" name="terms" required class="mt-0.5 rounded border-gray-300 text-purple-900 focus:ring-purple-900">
                    <label for="terms" class="text-[11px] text-gray-500 leading-snug">
                        En soumettant cette candidature, je m'engage à fournir des informations véridiques. Je comprends que ma candidature sera minutieusement examinée par l'administration.
                    </label>
                </div>

                <button type="submit" class="w-full bg-emerald-600 text-white font-bold text-xs py-3 rounded-xl hover:bg-emerald-700 transition shadow-sm">
                    Soumettre ma candidature
                </button>
            </form>
        <?php endif; ?>

    </div>
</body>
</html>