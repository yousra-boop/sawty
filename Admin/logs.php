<?php
session_start();
require_once("../auth/connexion.php");

// Guard Clause - Verify admin session
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Check if a search keyword was submitted
$search = trim($_GET['search'] ?? '');

if (!empty($search)) {
    // Search filter across item name or user email
    $stmtLogs = $pdo->prepare("SELECT * FROM Admin_Logs WHERE item_name LIKE :keyword OR user_email LIKE :keyword ORDER BY date_action DESC LIMIT 20");
    $stmtLogs->execute(['keyword' => "%$search%"]);
} else {
    // Pure, direct fetch from the Admin_Logs table as-is
    $stmtLogs = $pdo->query("SELECT * FROM Admin_Logs ORDER BY date_action DESC LIMIT 20");
}
$logs = $stmtLogs->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SAWTY - Journal des Actions</title>
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

    <!-- Brand Header -->
    <header class="bg-white border-b border-gray-100 shadow-sm h-16">
        <div class="max-w-7xl mx-auto px-6 h-full flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <span class="text-xl font-black text-sawty-purple tracking-wider">SAWTY ADMIN.</span>
                <span class="w-2 h-2 rounded-full bg-sawty-green"></span>
            </div>
            <nav class="flex items-center space-x-4 text-sm font-bold">
                <a href="admin_dashboard.php" class="text-gray-500 hover:text-sawty-purple transition">Retour au Dashboard</a>
                <a href="deconnexion.php" class="text-red-600 hover:text-red-800 transition">Sign Out</a>
            </nav>
        </div>
    </header>

    <!-- Main Workspace -->
    <main class="max-w-7xl mx-auto p-8">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <div>
                    <h2 class="text-lg font-bold text-sawty-purple">Journal des actions (Logs)</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Affichage direct des journaux générés par les déclencheurs (triggers).</p>
                </div>
                
                <!-- Search Filter Form -->
                <form action="" method="GET" class="flex items-center space-x-2 w-full md:w-auto">
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Rechercher..." class="border border-gray-200 px-3 py-2 rounded-xl text-xs focus:outline-none focus:border-sawty-purple w-full md:w-64">
                    <button type="submit" class="bg-sawty-purple text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-sawty-purpleHover transition">Filtrer</button>
                    <?php if (!empty($search)): ?>
                        <a href="logs.php" class="text-gray-400 hover:text-gray-600 text-xs font-bold px-2">Réinitialiser</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-gray-400 text-[10px] uppercase tracking-wider border-b border-gray-100">
                            <th class="pb-4 font-bold">Action</th>
                            <th class="pb-4 font-bold">Élément</th>
                            <th class="pb-4 font-bold">Utilisateur</th>
                            <th class="pb-4 font-bold text-right">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php if (empty($logs)): ?>
                            <tr>
                                <td colspan="4" class="py-12 text-center text-sm text-gray-400">Aucun journal trouvé.</td>
                            </tr>
                        <?php else: foreach ($logs as $log): ?>
                            <tr class="text-sm hover:bg-gray-50/50 transition">
                                <td class="py-4">
                                    <span class="px-2 py-1 rounded text-xs font-bold 
                                        <?= $log['action'] === 'INSERT' ? 'bg-emerald-100 text-emerald-700' : ($log['action'] === 'UPDATE' ? 'bg-blue-100 text-blue-700' : 'bg-rose-100 text-rose-700') ?>">
                                        <?= htmlspecialchars($log['action']) ?>
                                    </span>
                                </td>
                                <td class="py-4 font-bold text-gray-900"><?= htmlspecialchars($log['item_name']) ?></td>
                                <td class="py-4 text-xs text-sawty-purple font-medium"><?= htmlspecialchars($log['user_email'] ?? 'Système') ?></td>
                                <td class="py-4 text-xs text-gray-400 text-right font-mono"><?= htmlspecialchars($log['date_action']) ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>