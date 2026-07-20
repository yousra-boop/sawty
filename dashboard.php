<?php
session_start();
require_once("auth/connexion.php");

// Guard Clause
if (!isset($_SESSION['user_name'])) {
    header("Location: index.php");
    exit();
}

$elections = [];
$msg_error = "";

// Check for eligibility filter
if (isset($_GET['id_check']) && !empty($_GET['id_check'])) {
    $id_check = $_GET['id_check'];
    
    // MODIFIED: Using your actual table name 'ListesBlanches' and column 'identifiant'
    $sql = "SELECT e.* FROM Elections e 
            JOIN ListesBlanches lb ON e.id_election = lb.id_election 
            WHERE lb.identifiant = :code";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':code', $id_check);
    $stmt->execute();
    $elections = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($elections)) {
        $msg_error = "We are sorry, you are not qualified to participate in any elections yet. If you think this is a mistake, please reach out to your administration.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Sawty - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style/dashboard.css">
</head>
<body class="bg-gray-50">

    <header class="bg-white border-b border-gray-100 shadow-sm h-16">
        <div class="max-w-7xl mx-auto px-4 h-full flex items-center justify-between">
            <h1 class="text-xl font-black text-purple-900">SAWTY.</h1>
            <nav class="space-x-6 text-sm font-bold">
    <a href="#" class="text-gray-600">Mon Profil</a>
    <!-- UPDATE THIS LINE: -->
    <a href="candidat_signup.php" class="text-purple-600">Devenir Candidat</a>
    <a href="deconnexion.php" class="text-red-600">Sign Out</a>
</nav>
        </div>
    </header>

    <main class="max-w-7xl mx-auto p-8 grid grid-cols-12 gap-8">
        <!-- Left: Open Elections -->
        <aside class="col-span-3">
    <?php include('electionsactif.php'); ?>
</aside>

        <!-- Middle: Table -->
        <section class="col-span-6 bg-white p-6 rounded-2xl shadow-sm">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-gray-400 text-[10px] uppercase">
                        <th class="pb-4">Titre</th>
                        <th class="pb-4">Dates</th>
                        <th class="pb-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!isset($_GET['id_check'])): ?>
                        <tr><td colspan="3" class="py-10 text-center text-sm text-gray-400">Enter your ID to see your elections.</td></tr>
                    <?php elseif (!empty($msg_error)): ?>
                        <tr><td colspan="3" class="py-10 text-center text-sm text-red-500"><?php echo $msg_error; ?></td></tr>
                    <?php else: foreach ($elections as $elec): ?>
                        <tr class="border-t text-sm">
                            <td class="py-4 font-bold"><?php echo htmlspecialchars($elec['e_title']); ?></td>
                            <td class="py-4 text-xs"><?php echo $elec['start_date']; ?></td>
                            <td class="py-4">
                                 <a href="voter_dashboard.php?id=<?php echo $elec['id_election']; ?>" 
                                  class="btn-voter text-white px-3 py-1 rounded block text-center">
                                   Voter
                                </a>
                            </td>                      
                          </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </section>

        <!-- Right: Eligibility -->
        <aside class="col-span-3">
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <h3 class="font-bold mb-4">Check Eligibility</h3>
                <form action="massar_check.php" method="GET">
    <input type="text" name="id_check" class="w-full border p-2 mb-2 rounded" placeholder="Code Massar" required>
    <button type="submit" class="w-full bg-purple-900 text-white p-2 rounded">Check</button>
</form>
            </div>
        </aside>
    </main>
</body>
</html>