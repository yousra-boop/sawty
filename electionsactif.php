<?php
// This file is included in dashboard.php, so $pdo is available
$sidebar_stmt = $pdo->query("SELECT e_title, end_date FROM Elections WHERE status = 'Actif'");
$sidebar_elections = $sidebar_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="sidebar-box">
    <h3 class="font-bold mb-4">Scrutins Ouverts</h3>
    <div class="space-y-4">
        <?php if ($sidebar_elections): ?>
            <?php foreach ($sidebar_elections as $elec): ?>
                <div class="border-b border-gray-50 pb-2">
                    <p class="text-xs font-bold text-gray-800"><?php echo htmlspecialchars($elec['e_title']); ?></p>
                    <p class="text-[10px] text-gray-400">Fin : <?php echo date('d/m/Y', strtotime($elec['end_date'])); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-xs text-gray-400">Aucun scrutin actif pour le moment.</p>
        <?php endif; ?>
    </div>
</div>