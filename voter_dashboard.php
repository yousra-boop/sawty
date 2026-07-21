<?php
session_start();
require_once("auth/connexion.php");

// Guard Clause: Ensure user is logged in
if (!isset($_SESSION['user_name'])) {
    header("Location: index.php");
    exit();
}

// Get Election ID from URL parameter
$id_election = $_GET['id'] ?? null;

if (!$id_election) {
    header("Location: dashboard.php");
    exit();
}

// Fetch election details
$elec_stmt = $pdo->prepare("SELECT * FROM Elections WHERE id_election = ?");
$elec_stmt->execute([$id_election]);
$election = $elec_stmt->fetch(PDO::FETCH_ASSOC);

if (!$election) {
    header("Location: dashboard.php");
    exit();
}

// Fetch approved candidates for this election, joining users table to get their names
$cand_stmt = $pdo->prepare("
    SELECT c.*, u.user_name, u.user_surname 
    FROM Candidats c 
    JOIN users u ON c.id_user = u.id_user 
    WHERE c.id_election = ? AND c.c_status = 'approved'
");
$cand_stmt->execute([$id_election]);
$candidates = $cand_stmt->fetchAll(PDO::FETCH_ASSOC);

$total_candidates = count($candidates);
// Total steps: each candidate gets a step, plus 1 for the ballot step, plus 1 for the summary step
$total_steps = $total_candidates + 2; 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sawty - Bureau de Vote Électronique</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        royalPurple: '#4c1d95',
                        justiceGreen: '#059669',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 font-sans min-h-screen flex flex-col justify-between">

    <!-- Top Header Navigation -->
    <header class="bg-white border-b border-gray-100 shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <svg class="w-8 h-8 text-royalPurple" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="24" cy="20" r="6" fill="currentColor"/>
                    <path d="M12 44C12 35.1634 19.1634 28 28 28H30C33.123 28 36.0345 28.8929 38.5 30.435" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
                    <path d="M16 46H44V60H16V46Z" stroke="currentColor" stroke-width="4" stroke-linejoin="round"/>
                    <path d="M34 24L46 32L38 40" fill="#059669" opacity="0.8"/>
                    <rect x="34" y="24" width="16" height="12" rx="1" transform="rotate(-15 34 24)" stroke="currentColor" stroke-width="3" fill="white"/>
                </svg>
                <span class="text-xl font-black tracking-wider text-royalPurple">SAWTY<span class="text-justiceGreen">.</span></span>
            </div>
            <a href="dashboard.php" class="text-sm font-semibold text-gray-500 hover:text-red-600 transition">&larr; Quitter le scrutin</a>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-grow max-w-4xl mx-auto w-full px-4 py-8 space-y-6">

        <!-- Informational System Header Notice -->
        <div class="bg-amber-50 border border-amber-200 text-amber-950 rounded-2xl p-4 text-sm leading-relaxed flex items-start space-x-3">
            <span class="text-xl">💡</span>
            <div>
                <p class="font-bold text-amber-900">Vote Responsable & Éclairé</p>
                <p>Si aucun de ces candidats ne répond à vos exigences et que vous ne souhaitez soutenir personne, vous aurez l'option de **voter blanc (abstention citoyenne) et de faire entendre vos revendications de manière anonyme** à la fin de la présentation.</p>
            </div>
        </div>

        <div class="text-center space-y-1">
            <h1 class="text-2xl font-black text-gray-900"><?php echo htmlspecialchars($election['e_title']); ?></h1>
            <p class="text-xs text-gray-400 uppercase tracking-widest font-bold">Étape Obligatoire : Consultation des Professions de Foi</p>
        </div>

        <!-- 1. TOP BAR: CANDIDATES FACES AND NAMES -->
        <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm flex justify-center items-center gap-6 sm:gap-12 overflow-x-auto">
            <?php foreach ($candidates as $index => $cand): 
                $initials = strtoupper(substr($cand['user_name'], 0, 1) . substr($cand['user_surname'], 0, 1));
                $fullName = htmlspecialchars($cand['user_name'] . ' ' . $cand['user_surname']);
                // Highlight classes for the first item by default
                $isFirst = ($index === 0);
            ?>
                <div id="thumb-<?php echo $index; ?>" class="flex flex-col items-center space-y-2 <?php echo $isFirst ? 'opacity-100 scale-105 font-bold' : 'opacity-40 scale-95'; ?> transition-all duration-300">
                    <div class="w-14 h-14 <?php echo $isFirst ? 'bg-purple-600 ring-4 ring-purple-200' : 'bg-emerald-600'; ?> text-white font-bold rounded-full flex items-center justify-center text-lg shadow-md">
                        <?php echo $initials; ?>
                    </div>
                    <span class="text-xs <?php echo $isFirst ? 'text-gray-900 font-bold' : 'text-gray-500 font-medium'; ?>"><?php echo $fullName; ?></span>
                </div>
            <?php endforeach; ?>

            <!-- Thumbnail Ballot Release Trigger Entry -->
            <div id="thumb-ballot" class="flex flex-col items-center space-y-2 opacity-20 scale-95 transition-all duration-300">
                <div class="w-14 h-14 bg-gray-800 text-white font-bold rounded-full flex items-center justify-center text-lg shadow-md">📥</div>
                <span class="text-xs font-medium text-gray-500">Bulletin</span>
            </div>
        </div>

        <!-- 2. MAIN PROMINENT FOCUS BOX -->
        <div class="bg-white border border-gray-100 shadow-xl rounded-2xl p-6 sm:p-8 relative min-h-[400px] flex flex-col justify-between" id="presentation-container">
            
            <form action="cast_vote.php" method="POST" id="main-vote-form" class="space-y-6 flex-grow flex flex-col justify-between">
                <input type="hidden" name="id_election" value="<?php echo htmlspecialchars($election['id_election']); ?>">

                <?php if (empty($candidates)): ?>
                    <div class="text-center py-12 text-gray-400 text-sm">
                        Aucun candidat approuvé n'est disponible pour le moment dans ce scrutin. Vous pouvez tout de même exprimer un vote blanc.
                    </div>
                <?php else: ?>
                    <!-- DYNAMIC CANDIDATE VIEWS -->
                    <?php foreach ($candidates as $index => $cand): 
                        $fullName = htmlspecialchars($cand['user_name'] . ' ' . $cand['user_surname']);
                        $initials = strtoupper(substr($cand['user_name'], 0, 1) . substr($cand['user_surname'], 0, 1));
                    ?>
                        <div id="candidate-view-<?php echo $index; ?>" class="space-y-6 <?php echo $index === 0 ? 'block' : 'hidden'; ?>">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-gray-100 pb-4 gap-4">
                                <div class="flex items-center space-x-4">
                                    <div class="w-16 h-16 bg-purple-100 rounded-xl flex items-center justify-center text-royalPurple text-xl font-bold"><?php echo $initials; ?></div>
                                    <div>
                                        <h3 class="text-xl font-black text-gray-900"><?php echo $fullName; ?></h3>
                                        <p class="text-sm font-semibold text-justiceGreen">Candidat officiel</p>
                                    </div>
                                </div>
                                <span class="bg-purple-100 text-purple-800 text-xs px-3 py-1 rounded-full font-bold uppercase tracking-wider">Candidat <?php echo ($index + 1); ?> sur <?php echo $total_candidates; ?></span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="md:col-span-2 space-y-4">
                                    <h4 class="font-bold text-gray-900 text-sm tracking-wider uppercase">✨ Ma biographie & Programme :</h4>
                                    <p class="text-gray-600 text-sm leading-relaxed whitespace-pre-line">
                                        <?php echo !empty($cand['c_motivation']) ? htmlspecialchars($cand['c_motivation']) : "Aucune biographie fournie pour le moment."; ?>
                                    </p>
                                </div>
                                <div class="bg-purple-50/50 rounded-xl p-4 space-y-2 border border-purple-100">
                                    <h4 class="font-bold text-purple-950 text-xs tracking-wider uppercase">🎯 Informations :</h4>
                                    <p class="text-xs text-purple-900">Examinez attentivement le profil de ce candidat avant de passer à l'étape suivante du vote secret.</p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- STEP: THE BALLOT BOX SELECTION -->
                <div id="ballot-view" class="space-y-6 hidden">
                    <div class="border-b border-gray-100 pb-4 text-center">
                        <h3 class="text-xl font-black text-gray-900">🗳️ Faites votre Choix Secret</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Sélectionnez une option pour débloquer l'étape de confirmation.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <?php foreach ($candidates as $cand): 
                            $fullName = $cand['user_name'] . ' ' . $cand['user_surname'];
                        ?>
                            <label class="block cursor-pointer">
                                <input type="radio" name="id_candidat" value="<?php echo htmlspecialchars($fullName); ?>" class="peer sr-only" onchange="enableBallotNext()">
                                <div class="p-4 border border-gray-200 rounded-xl text-center font-bold text-gray-700 peer-checked:bg-royalPurple peer-checked:text-white peer-checked:border-royalPurple hover:bg-gray-50 transition shadow-sm">
                                    Voter pour <?php echo htmlspecialchars($fullName); ?>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <div class="bg-amber-50/50 border border-amber-200 rounded-xl p-4 space-y-3">
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="checkbox" id="user_protest" name="user_protest" value="1" class="w-4 h-4 text-amber-600 rounded" onchange="handleProtestToggle()">
                            <span class="text-sm font-bold text-amber-950">Exprimer un vote blanc de protestation</span>
                        </label>
                        <textarea id="protest_reason" name="protest_reason" rows="2" placeholder="Pourquoi aucun candidat ne vous représente ? Vos remarques resteront anonymes..." 
                                  class="w-full p-2.5 bg-white border border-amber-200 rounded-xl text-xs focus:ring-1 focus:ring-amber-500 outline-none text-gray-900"></textarea>
                    </div>
                </div>

                <!-- STEP: THE CONFIRMATION SUMMARY PAGE -->
                <div id="confirmation-view" class="space-y-6 hidden">
                    <div class="border-b border-gray-100 pb-4 text-center">
                        <h3 class="text-xl font-black text-gray-900">🔍 Récapitulatif de votre Bulletin</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Veuillez relire attentivement votre choix avant l'envoi définitif.</p>
                    </div>

                    <!-- Visual Envelope Receipt -->
                    <div class="bg-gray-50 border-2 border-dashed border-gray-200 rounded-2xl p-6 max-w-md mx-auto text-center space-y-3">
                        <span class="text-3xl text-royalPurple">✉️</span>
                        <p class="text-xs uppercase font-bold tracking-wider text-gray-400">Contenu de votre enveloppe numérique</p>
                        <p id="summary-choice-text" class="text-xl font-black text-royalPurple">Aucun choix sélectionné</p>
                        <p id="summary-protest-note" class="text-xs text-amber-600 italic hidden">Accompagné d'une note d'abstention citoyenne</p>
                    </div>

                    <!-- Agreement Checkbox -->
                    <div class="max-w-md mx-auto">
                        <label class="flex items-start space-x-3 bg-purple-50/40 p-3 rounded-xl border border-purple-100 cursor-pointer">
                            <input type="checkbox" id="agree-terms" class="w-4 h-4 mt-0.5 text-royalPurple rounded" onchange="toggleFinalSubmitBtn()">
                            <span class="text-xs text-purple-950 leading-tight">
                                Je confirme que ce choix représente ma volonté électorale libre. Je comprends qu'après validation, mon vote sera chiffré et qu'aucune modification ne sera possible.
                            </span>
                        </label>
                    </div>

                    <!-- Ultimate Final Trigger Submit -->
                    <div class="max-w-md mx-auto">
                        <button type="submit" id="final-submit-btn" disabled
                                class="w-full bg-justiceGreen hover:bg-opacity-95 text-white font-bold py-3 px-4 rounded-xl transition shadow-md disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed">
                            Confirmer et Voter Définitivement
                        </button>
                    </div>
                </div>

            </form>

            <!-- BUTTON CONTROLS FOOTER -->
            <div class="flex items-center justify-between border-t border-gray-100 pt-4 mt-6">
                <button id="btn-prev" onclick="changeSlide(-1)" class="px-4 py-2 text-sm font-bold text-gray-400 bg-gray-100 rounded-xl cursor-not-allowed transition" disabled>
                    &larr; Précédent
                </button>
                
                <div class="text-xs text-gray-400 font-bold tracking-wider uppercase" id="view-indicator">
                    Étape 1 sur <?php echo $total_steps; ?>
                </div>

                <button id="btn-next" onclick="changeSlide(1)" class="px-5 py-2.5 text-sm font-bold text-white bg-royalPurple hover:bg-opacity-95 rounded-xl transition shadow-sm">
                    Suivant &rarr;
                </button>
            </div>

        </div>
    </main>

    <footer class="bg-white border-t border-gray-100 py-6 text-center text-xs text-gray-400">
        <div class="max-w-7xl mx-auto px-4">
            <p>&copy; 2026 Sawty — Parcours Électoral Certifié Réfléchi.</p>
        </div>
    </footer>

    <!-- 3. INTERACTIVE SLIDER JAVASCRIPT CONTROLLER -->
    <script>
        let currentStep = 0; 
        const totalCandidates = <?php echo $total_candidates; ?>;
        const totalSteps = <?php echo $total_steps; ?>;

        function changeSlide(direction) {
            // Form compilation data lookup before step transitions into summary view (last step)
            if (currentStep === totalCandidates && direction === 1) {
                updateSummaryDetails();
            }

            // Hide all candidate views, ballot view, and confirmation view
            for (let i = 0; i < totalCandidates; i++) {
                const view = document.getElementById(`candidate-view-${i}`);
                if (view) view.classList.add('hidden');
            }
            const ballotView = document.getElementById('ballot-view');
            if (ballotView) ballotView.classList.add('hidden');
            
            const confView = document.getElementById('confirmation-view');
            if (confView) confView.classList.add('hidden');

            // Reset current thumbnails containers states
            for (let i = 0; i < totalCandidates; i++) {
                const thumb = document.getElementById(`thumb-${i}`);
                if (thumb) {
                    thumb.className = "flex flex-col items-center space-y-2 opacity-40 scale-95 transition-all duration-300";
                    thumb.querySelector('div').className = "w-14 h-14 bg-emerald-600 text-white font-bold rounded-full flex items-center justify-center text-lg shadow-md";
                }
            }
            const thumbBallot = document.getElementById('thumb-ballot');
            if (thumbBallot) {
                thumbBallot.className = "flex flex-col items-center space-y-2 opacity-40 scale-95 transition-all duration-300";
                thumbBallot.querySelector('div').classList.remove('ring-4', 'ring-gray-400', 'ring-purple-400');
            }

            // Manage steps counters
            currentStep += direction;

            const prevBtn = document.getElementById('btn-prev');
            const nextBtn = document.getElementById('btn-next');
            const indicator = document.getElementById('view-indicator');

            // Control standard Back Button navigation states
            if (currentStep === 0) {
                prevBtn.disabled = true;
                prevBtn.className = "px-4 py-2 text-sm font-bold text-gray-400 bg-gray-100 rounded-xl cursor-not-allowed transition";
            } else {
                prevBtn.disabled = false;
                prevBtn.className = "px-4 py-2 text-sm font-bold text-purple-900 bg-purple-50 hover:bg-purple-100 rounded-xl transition";
            }

            // Display correct layouts & highlight top badges based on step index
            if (currentStep < totalCandidates) {
                // Candidate steps
                const activeView = document.getElementById(`candidate-view-${currentStep}`);
                if (activeView) activeView.classList.remove('hidden');

                const activeThumb = document.getElementById(`thumb-${currentStep}`);
                if (activeThumb) {
                    activeThumb.className = "flex flex-col items-center space-y-2 opacity-100 scale-105 transition-all duration-300 font-bold";
                    activeThumb.querySelector('div').className = "w-14 h-14 bg-purple-600 text-white font-bold rounded-full flex items-center justify-center text-lg shadow-md ring-4 ring-purple-200";
                }
                nextBtn.classList.remove('hidden');
                indicator.innerText = `Étape ${currentStep + 1} sur ${totalSteps}`;
            } else if (currentStep === totalCandidates) {
                // Ballot selection step
                if (ballotView) ballotView.classList.remove('hidden');
                if (thumbBallot) {
                    thumbBallot.className = "flex flex-col items-center space-y-2 opacity-100 scale-105 transition-all duration-300 font-bold";
                    thumbBallot.querySelector('div').classList.add('ring-4', 'ring-gray-400');
                }
                nextBtn.classList.remove('hidden');
                indicator.innerText = `Étape ${currentStep + 1} sur ${totalSteps}`;
                enableBallotNext(); // Evaluate if they have selected anything already
            } else if (currentStep === totalSteps - 1) {
                // Confirmation summary step
                if (confView) confView.classList.remove('hidden');
                if (thumbBallot) {
                    thumbBallot.className = "flex flex-col items-center space-y-2 opacity-100 scale-105 transition-all duration-300 font-bold";
                    thumbBallot.querySelector('div').classList.add('ring-4', 'ring-purple-400');
                }
                nextBtn.classList.add('hidden'); // Hide footer next button, form final button takes over
                indicator.innerText = `Étape ${totalSteps} sur ${totalSteps} : Confirmation`;
            }
        }

        // Protect Ballot Step: Don't let them step forward to the summary without selecting an option
        function enableBallotNext() {
            const nextBtn = document.getElementById('btn-next');
            const candidates = document.getElementsByName('id_candidat');
            const protestCheck = document.getElementById('user_protest');
            
            let checked = false;
            for (let i = 0; i < candidates.length; i++) {
                if (candidates[i].checked) checked = true;
            }
            if (protestCheck && protestCheck.checked) checked = true;

            if (checked && currentStep === totalCandidates) {
                nextBtn.disabled = false;
                nextBtn.className = "px-5 py-2.5 text-sm font-bold text-white bg-royalPurple hover:bg-opacity-95 rounded-xl transition shadow-sm";
            } else if (!checked && currentStep === totalCandidates) {
                nextBtn.disabled = true;
                nextBtn.className = "px-5 py-2.5 text-sm font-bold text-gray-300 bg-gray-100 rounded-xl cursor-not-allowed transition";
            }
        }

        function handleProtestToggle() {
            const protestCheck = document.getElementById('user_protest');
            const candidates = document.getElementsByName('id_candidat');
            
            if (protestCheck.checked) {
                // Uncheck candidate radios if user chooses to abstain/protest explicitly
                for (let i = 0; i < candidates.length; i++) {
                    candidates[i].checked = false;
                }
            }
            enableBallotNext();
        }

        // Dynamic runtime mapping from ballot selectors straight into summary text UI elements
        function updateSummaryDetails() {
            const candidates = document.getElementsByName('id_candidat');
            const protestCheck = document.getElementById('user_protest');
            const txtSummary = document.getElementById('summary-choice-text');
            const noteSummary = document.getElementById('summary-protest-note');

            let selectedValue = "";
            for (let i = 0; i < candidates.length; i++) {
                if (candidates[i].checked) selectedValue = candidates[i].value;
            }

            if (protestCheck && protestCheck.checked) {
                txtSummary.innerText = "VOTE BLANC (Abstention)";
                txtSummary.className = "text-xl font-black text-amber-700 uppercase tracking-wide";
                noteSummary.classList.remove('hidden');
            } else if (selectedValue !== "") {
                txtSummary.innerText = "Bulletin : " + selectedValue;
                txtSummary.className = "text-xl font-black text-purple-900";
                noteSummary.classList.add('hidden');
            }
        }

        // Control ultimate green submission gate
        function toggleFinalSubmitBtn() {
            const agreeCheck = document.getElementById('agree-terms');
            const submitBtn = document.getElementById('final-submit-btn');
            submitBtn.disabled = !agreeCheck.checked;
        }
    </script>

</body>
</html>