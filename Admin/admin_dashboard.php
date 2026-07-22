<?php
session_start();
require_once("../auth/connexion.php");

// 1. Guard Clause - Verify admin session
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// 2. Security Check - Force password change for temporary accounts
if (isset($_SESSION['is_temp']) && $_SESSION['is_temp'] == 1) {
    header("Location: change_password.php");
    exit();
}

$msg_success = "";
$msg_error = "";
// ... (rest of your code remains exactly the same)

// 2. Handle Backend CRUD Actions directly inside the file
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // A. ADD ELECTION
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        $title = trim($_POST['e_title']);
        $description = trim($_POST['e_description']);
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $admin_id = $_SESSION['admin_id'];

        if (!empty($title) && !empty($start_date) && !empty($end_date)) {
            $stmt = $pdo->prepare("INSERT INTO Elections (e_title, e_description, start_date, end_date, id_admin) VALUES (:title, :description, :start_date, :end_date, :id_admin)");
            
            if ($stmt->execute([
                'title' => $title, 
                'description' => $description,
                'start_date' => $start_date, 
                'end_date' => $end_date,
                'id_admin' => $admin_id
            ])) {
                $msg_success = "L'élection a été ajoutée avec succès !";
            } else {
                $msg_error = "Erreur lors de l'ajout de l'élection.";
            }
        }
    }
    
   if (isset($_POST['action']) && $_POST['action'] === 'edit') {
        $id_election = intval($_POST['id_election']);
        $title = trim($_POST['e_title']);
        $description = trim($_POST['e_description']);
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];

        $stmt = $pdo->prepare("UPDATE Elections SET e_title = :title, e_description = :description, start_date = :start_date, end_date = :end_date WHERE id_election = :id");
        if ($stmt->execute(['title' => $title, 'description' => $description, 'start_date' => $start_date, 'end_date' => $end_date, 'id' => $id_election])) {
            $msg_success = "L'élection a été modifiée avec succès !";
        } else {
            $msg_error = "Erreur lors de la modification.";
        }
    }

    // C. DELETE ELECTION
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id_election = intval($_POST['id_election']);
        $stmt = $pdo->prepare("DELETE FROM Elections WHERE id_election = :id");
        if ($stmt->execute(['id' => $id_election])) {
            $msg_success = "L'élection a été supprimée avec succès !";
        } else {
            $msg_error = "Erreur lors de la suppression.";
        }
    }
    // D. TOGGLE VOTE STATUS (Open / Close)
    if (isset($_POST['action']) && $_POST['action'] === 'toggle_status') {
        $id_election = intval($_POST['id_election']);
        $current_status = $_POST['current_status'];
        
        // Flip the status: if it's active, close it. Otherwise, make it active.
        $new_status = ($current_status === 'active') ? 'closed' : 'active';
        
        // If opening the votes, we can also dynamically set the start_date to NOW if it was pending
        $stmt = $pdo->prepare("UPDATE Elections SET status = :status WHERE id_election = :id");
        if ($stmt->execute(['status' => $new_status, 'id' => $id_election])) {
            $msg_success = "Le statut de l'élection a été mis à jour avec succès ($new_status) !";
        } else {
            $msg_error = "Erreur lors de la modification du statut.";
        }
    }
}

// 3. Fetch current active elections to populate the dynamic table
$stmt = $pdo->query("SELECT * FROM Elections ORDER BY id_election DESC");
$elections = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SAWTY - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        sawty: {
                            purple: '#4c1d95', /* Royal Purple */
                            purpleHover: '#3b0764',
                            green: '#10b981',  /* Trust Green Accent */
                            greenHover: '#059669'
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
                <span class="text-gray-500 font-normal">Session Administrateur Active</span>
                <a href="../index.php" class="text-red-600 hover:text-red-800 transition">Sign Out</a>
            </nav>
        </div>
    </header>

    <!-- Main Workspace -->
    <main class="max-w-7xl mx-auto p-8 grid grid-cols-12 gap-8">
        
   <!-- Left Column: Elegant Navigation Sidebar -->
        <aside class="col-span-3">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 sticky top-8">
                <h3 class="font-bold text-gray-400 text-[10px] uppercase tracking-wider mb-4">Panneau de Gestion</h3>
                <nav class="space-y-1">
                    <a href="#" class="flex items-center space-x-3 bg-purple-50 text-sawty-purple font-bold px-4 py-3 rounded-xl transition">
                        <span class="w-2 h-2 rounded-full bg-sawty-purple"></span>
                        <span>Élections</span>
                    </a>

                    <a href="pending_candidates.php" class="flex items-center space-x-3 text-gray-600 hover:bg-gray-50 font-bold px-4 py-3 rounded-xl transition">
                        <span class="w-2 h-2 rounded-full bg-transparent"></span>
                        <span>Candidatures</span>
                    </a>

                    <a href="logs.php" class="flex items-center space-x-3 text-gray-600 hover:bg-gray-50 font-bold px-4 py-3 rounded-xl transition">
                        <span class="w-2 h-2 rounded-full bg-transparent"></span>
                        <span>Journal des Logs</span>
                    </a>

                    <a href="listes_blanches.php" class="flex items-center space-x-3 text-gray-600 hover:bg-gray-50 font-bold px-4 py-3 rounded-xl transition">
                        <span class="w-2 h-2 rounded-full bg-transparent"></span>
                        <span>Listes Blanches</span>
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Right Content Block: Dynamic CRUD Area -->
        <section class="col-span-9 space-y-6">
            
            <!-- Alert Notifications -->
            <?php if (!empty($msg_success)): ?>
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm font-semibold">
                    ✓ <?php echo $msg_success; ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($msg_error)): ?>
                <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl text-sm font-semibold">
                    ⚠ <?php echo $msg_error; ?>
                </div>
            <?php endif; ?>

            <!-- Table Block Interface -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-lg font-bold text-sawty-purple">Gestion des Élections</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Ajoutez, modifiez ou clôturez les sessions électorales en temps réel.</p>
                    </div>
                        <a href="nvl_election.php" class="bg-sawty-green text-white px-4 py-2 rounded-xl text-xs font-bold hover:opacity-90 transition shadow-sm">
                            + Ajouter une élection
                        </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-gray-400 text-[10px] uppercase tracking-wider border-b border-gray-100">
                                <th class="pb-4 font-bold">Affiche</th> <!-- ADDED HERE -->
                                <th class="pb-4 font-bold">Titre de l'élection</th>
                                <th class="pb-4 font-bold">Date de Début</th>
                                <th class="pb-4 font-bold">Date de Fin</th>
                                <th class="pb-4 font-bold text-right">Actions de Contrôle</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php if (empty($elections)): ?>
                                <tr>
                                    <td colspan="4" class="py-12 text-center text-sm text-gray-400">Aucune élection programmée pour le moment.</td>
                                </tr>
                            <?php else: foreach ($elections as $elec): ?>
                                <tr class="text-sm hover:bg-gray-50/50 transition">
    <!-- Poster Thumbnail -->
    <td class="py-4">
        <?php if (!empty($elec['poster'])): ?>
            <img src="../uploads/<?php echo htmlspecialchars($elec['poster']); ?>" alt="Poster" class="w-10 h-10 object-cover rounded-lg shadow-sm border border-gray-100">
        <?php else: ?>
            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center text-[10px] text-gray-400 font-bold">N/A</div>
        <?php endif; ?>
    </td>
    
    <!-- Title & Status Badge -->
    <td class="py-4">
        <div class="font-bold text-gray-900"><?php echo htmlspecialchars($elec['e_title']); ?></div>
        <div class="mt-1">
            <?php if ($elec['status'] === 'active'): ?>
                <span class="bg-emerald-50 text-emerald-600 border border-emerald-200 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">● Ouvert (Active)</span>
            <?php elseif ($elec['status'] === 'closed'): ?>
                <span class="bg-rose-50 text-rose-600 border border-rose-200 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">■ Fermé (Closed)</span>
            <?php else: ?>
                <span class="bg-amber-50 text-amber-600 border border-amber-200 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">⏳ En attente (Pending)</span>
            <?php endif; ?>
        </div>
    </td>

    <td class="py-4 text-xs text-gray-500 font-mono"><?php echo $elec['start_date']; ?></td>
    <td class="py-4 text-xs text-gray-500 font-mono"><?php echo $elec['end_date']; ?></td>
    
    <!-- Control Actions -->
    <td class="py-4 text-right space-x-3 font-semibold text-xs">
        <!-- Toggle Open/Close Button -->
        <form action="" method="POST" class="inline">
            <input type="hidden" name="action" value="toggle_status">
            <input type="hidden" name="id_election" value="<?php echo $elec['id_election']; ?>">
            <input type="hidden" name="current_status" value="<?php echo $elec['status']; ?>">
            
            <?php if ($elec['status'] === 'active'): ?>
                <button type="submit" class="text-amber-600 hover:text-amber-800 transition font-bold">Fermer les votes</button>
            <?php else: ?>
                <button type="submit" class="text-emerald-600 hover:text-emerald-800 transition font-bold">Ouvrir les votes</button>
            <?php endif; ?>
        </form>

        <!-- Edit Button -->
        <a href="nvl_election.php?id=<?php echo $elec['id_election']; ?>" class="text-sawty-purple hover:text-sawty-purpleHover transition">
            Modifier
        </a>

        <!-- Delete Button -->
        <form action="" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer définitivement cette élection ?');">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id_election" value="<?php echo $elec['id_election']; ?>">
            <button type="submit" class="text-rose-600 hover:text-rose-800 transition">Supprimer</button>
        </form>
    </td>
</tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>

    <!-- Modal Form: Add Election -->
    <div id="add-modal" class="hidden fixed inset-0 bg-gray-900/40 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-2xl w-full max-w-md shadow-xl border border-gray-100">
            <h3 class="font-bold text-lg text-sawty-purple mb-4">Créer une Élection</h3>
            <form action="" method="POST" class="space-y-4">
                <input type="hidden" name="action" value="add">
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Titre</label>
                    <input type="text" name="e_title" required class="w-full border border-gray-200 px-3 py-2 rounded-xl text-sm focus:outline-none focus:border-sawty-purple">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Date Début</label>
                        <input type="datetime-local" name="start_date" required class="w-full border border-gray-200 px-3 py-2 rounded-xl text-sm focus:outline-none focus:border-sawty-purple">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Date Fin</label>
                        <input type="datetime-local" name="end_date" required class="w-full border border-gray-200 px-3 py-2 rounded-xl text-sm focus:outline-none focus:border-sawty-purple">
                    </div>
                </div>
                <div class="flex justify-end space-x-3 pt-2">
                    <button type="button" onclick="toggleModal('add-modal')" class="text-gray-500 font-bold text-xs px-4 py-2 hover:bg-gray-100 rounded-xl transition">Annuler</button>
                    <button type="submit" class="bg-sawty-purple text-white font-bold text-xs px-4 py-2 rounded-xl hover:bg-sawty-purpleHover transition">Confirmer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Form: Edit Election -->
    <div id="edit-modal" class="hidden fixed inset-0 bg-gray-900/40 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-2xl w-full max-w-md shadow-xl border border-gray-100">
            <h3 class="font-bold text-lg text-sawty-purple mb-4">Modifier l'Élection</h3>
            <form action="" method="POST" class="space-y-4">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id_election" id="edit-id">
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Titre</label>
                    <input type="text" name="e_title" id="edit-title" required class="w-full border border-gray-200 px-3 py-2 rounded-xl text-sm focus:outline-none focus:border-sawty-purple">
                </div>
                <!-- Add this block inside your <form> tag -->
<div>
    <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Description</label>
    <textarea name="e_description" rows="3" required class="w-full border border-gray-200 px-3 py-2 rounded-xl text-sm focus:outline-none focus:border-sawty-purple"></textarea>
</div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Date Début</label>
                        <input type="datetime-local" name="start_date" id="edit-start" required class="w-full border border-gray-200 px-3 py-2 rounded-xl text-sm focus:outline-none focus:border-sawty-purple">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Date Fin</label>
                        <input type="datetime-local" name="end_date" id="edit-end" required class="w-full border border-gray-200 px-3 py-2 rounded-xl text-sm focus:outline-none focus:border-sawty-purple">
                    </div>
                </div>
                <div class="flex justify-end space-x-3 pt-2">
                    <button type="button" onclick="toggleModal('edit-modal')" class="text-gray-500 font-bold text-xs px-4 py-2 hover:bg-gray-100 rounded-xl transition">Annuler</button>
                    <button type="submit" class="bg-sawty-green text-white font-bold text-xs px-4 py-2 rounded-xl hover:bg-sawty-greenHover transition">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- UI Interaction Scripts -->
    <script>
        function toggleModal(id) {
            const modal = document.getElementById(id);
            modal.classList.toggle('hidden');
        }

        function openEditModal(id, title, start, end) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-title').value = title;
            
            // Format datetime strings to fit HTML5 input values (YYYY-MM-DDTHH:MM)
            document.getElementById('edit-start').value = start.replace(" ", "T").substring(0, 16);
            document.getElementById('edit-end').value = end.replace(" ", "T").substring(0, 16);
            
            toggleModal('edit-modal');
        }
    </script>
</body>
</html>