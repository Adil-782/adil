<?php
// Connexion et Header
include 'includes/db_connect.php';
include 'includes/header.php';

// Liste des challenges
$challenges = [
    [
        'title' => 'The Steam Employee Leak',
        'description' => 'Un employé de Valve, Adil, a posté une photo de son bureau sur Twitter. Son identifiant est visible : adil_dev. Le serveur n\'a aucune protection contre les tentatives répétées...',
        'difficulty' => 'easy',
        'points' => 100,
        'file' => 'challenges/admin-login.php',
        'icon' => '🔓'
    ],
    [
        'title' => 'Operation: Gabe\'s Hidden Server',
        'description' => 'Le serveur de pré-production Steam Deck 2 permet de modifier les avatars. Si vous arrivez à uploader un script PHP au lieu d\'une image, le serveur sera à vous...',
        'difficulty' => 'hard',
        'points' => 300,
        'file' => 'challenges/upload-avatar.php',
        'icon' => '📤'
    ],
    [
        'title' => 'Password Recovery Heist',
        'description' => 'Nassim - Le mécanisme de réinitialisation de mot de passe utilise l\'en-tête Host HTTP sans validation. Manipulez-le pour intercepter les tokens de reset...',
        'difficulty' => 'insane',
        'points' => 500,
        'file' => 'reset-password.php',
        'icon' => '🔐'
    ],
    [
        'title' => 'Database Treasure Hunt',
        'description' => 'Nathan - La recherche de jeux est vulnérable aux injections SQL. Combinez avec IDOR pour extraire les clés de jeux d\'autres utilisateurs et révéler le flag caché...',
        'difficulty' => 'hard',
        'points' => 400,
        'file' => 'search.php',
        'icon' => '💎'
    ],
    [
        'title' => 'Community Takeover',
        'description' => 'Titouan - Les avis de jeux ne sont pas filtrés et les cookies n\'ont pas HttpOnly. Injectez du JavaScript pour voler la session admin et obtenir le flag ultime...',
        'difficulty' => 'insane',
        'points' => 500,
        'file' => 'game.php?id=1',
        'icon' => '🍪'
    ],
];

$difficulties = ['easy' => 'Facile', 'medium' => 'Moyen', 'hard' => 'Difficile', 'insane' => 'Insane'];
?>

<style>
    .challenges-header {
        background: linear-gradient(135deg, rgba(15, 20, 30, 0.95) 0%, rgba(20, 30, 50, 0.9) 100%);
        padding: 40px;
        border-radius: var(--radius-lg);
        margin-bottom: 30px;
        border: 1px solid rgba(102, 192, 244, 0.2);
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .challenges-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #ff6b6b, #feca57, #48dbfb, #ff9ff3, #54a0ff);
    }

    .challenges-header h1 {
        font-size: 36px;
        background: linear-gradient(135deg, #fff 0%, var(--accent-cyan) 50%, #ff6b6b 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 10px;
    }

    .challenges-header p {
        color: var(--text-muted);
        font-size: 16px;
    }

    .stats-row {
        display: flex;
        justify-content: center;
        gap: 40px;
        margin-top: 25px;
    }

    .stat-item {
        text-align: center;
    }

    .stat-value {
        font-size: 32px;
        font-weight: 800;
        color: var(--accent-blue);
    }

    .stat-label {
        font-size: 12px;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .filter-bar {
        display: flex;
        gap: 10px;
        margin-bottom: 25px;
        flex-wrap: wrap;
    }

    .filter-btn {
        padding: 10px 20px;
        background: var(--bg-card);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: var(--radius-md);
        color: var(--text-secondary);
        cursor: pointer;
        transition: var(--transition-normal);
        font-weight: 600;
        font-size: 13px;
    }

    .filter-btn:hover,
    .filter-btn.active {
        background: var(--accent-blue);
        color: var(--bg-dark);
        border-color: var(--accent-blue);
    }

    .challenges-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 25px;
    }

    .challenge-card {
        background: linear-gradient(145deg, var(--bg-card) 0%, rgba(22, 32, 45, 0.95) 100%);
        border-radius: var(--radius-lg);
        border: 1px solid rgba(255, 255, 255, 0.05);
        overflow: hidden;
        transition: var(--transition-normal);
        text-decoration: none;
        display: block;
    }

    .challenge-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
        border-color: rgba(102, 192, 244, 0.4);
    }

    .challenge-header {
        padding: 25px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .challenge-meta {
        flex: 1;
    }

    .challenge-category {
        display: inline-block;
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 12px;
    }

    .category-web {
        background: rgba(54, 160, 255, 0.2);
        color: #54a0ff;
    }

    .category-crypto {
        background: rgba(255, 159, 243, 0.2);
        color: #ff9ff3;
    }

    .category-forensics {
        background: rgba(72, 219, 251, 0.2);
        color: #48dbfb;
    }

    .category-misc {
        background: rgba(165, 177, 194, 0.2);
        color: #a5b1c2;
    }

    .challenge-title {
        font-size: 20px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 8px;
    }

    .challenge-card:hover .challenge-title {
        color: var(--accent-blue);
    }

    .challenge-difficulty {
        font-size: 12px;
        font-weight: 600;
    }

    .diff-easy {
        color: #1dd1a1;
    }

    .diff-medium {
        color: #feca57;
    }

    .diff-hard {
        color: #ff6b6b;
    }

    .diff-insane {
        color: #ff00ff;
        text-shadow: 0 0 10px #ff00ff;
    }

    .challenge-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--bg-hover) 0%, rgba(102, 192, 244, 0.2) 100%);
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        transition: var(--transition-normal);
    }

    .challenge-card:hover .challenge-icon {
        transform: scale(1.1) rotate(5deg);
    }

    .challenge-body {
        padding: 25px;
    }

    .challenge-desc {
        color: var(--text-muted);
        font-size: 14px;
        line-height: 1.7;
        margin-bottom: 20px;
        min-height: 60px;
    }

    .challenge-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .challenge-points {
        font-size: 22px;
        font-weight: 800;
        color: var(--accent-green-light);
    }

    .challenge-points span {
        font-size: 12px;
        color: var(--text-muted);
        font-weight: 400;
    }

    .start-btn {
        padding: 10px 25px;
        background: linear-gradient(135deg, var(--accent-blue-light) 0%, var(--accent-blue-dark) 100%);
        border-radius: var(--radius-md);
        color: white;
        font-weight: 700;
        font-size: 13px;
        text-transform: uppercase;
    }

    .challenge-card:hover .start-btn {
        box-shadow: 0 5px 20px rgba(102, 192, 244, 0.4);
    }

    .empty-state {
        text-align: center;
        padding: 80px 40px;
        background: var(--bg-card);
        border-radius: var(--radius-lg);
        border: 1px dashed rgba(102, 192, 244, 0.3);
    }

    .empty-state .icon {
        font-size: 64px;
        margin-bottom: 20px;
    }

    .empty-state h3 {
        color: var(--text-primary);
        margin-bottom: 10px;
    }

    .empty-state p {
        color: var(--text-muted);
    }

    /* Scenario box */
    .scenario-box {
        background: rgba(0, 0, 0, 0.3);
        border-left: 3px solid var(--accent-blue);
        padding: 12px 15px;
        margin-bottom: 15px;
        border-radius: 0 8px 8px 0;
    }

    .scenario-box .type {
        font-size: 10px;
        color: var(--accent-blue);
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 700;
    }
</style>

<div class="challenges-header">
    <h1>🚩 Challenges CTF</h1>
    <p>Testez vos compétences en cybersécurité et trouvez les flags cachés !</p>

    <div class="stats-row">
        <div class="stat-item">
            <div class="stat-value"><?php echo count($challenges); ?></div>
            <div class="stat-label">Challenges</div>
        </div>

        <div class="stat-item">
            <div class="stat-value"><?php echo array_sum(array_column($challenges, 'points')); ?></div>
            <div class="stat-label">Points Total</div>
        </div>
    </div>
</div>



<div class="challenges-grid">
    <?php if (empty($challenges)): ?>
        <div class="empty-state" style="grid-column: 1 / -1;">
            <div class="icon">🔒</div>
            <h3>Aucun challenge disponible</h3>
            <p>Les challenges arrivent bientôt... Restez connectés !</p>
        </div>
    <?php else: ?>
        <?php foreach ($challenges as $ch): ?>
            <a href="<?php echo $ch['file']; ?>" class="challenge-card">
                <div class="challenge-header">
                    <div class="challenge-meta">
                        <div class="challenge-title"><?php echo htmlspecialchars($ch['title']); ?></div>
                        <span class="challenge-difficulty diff-<?php echo $ch['difficulty']; ?>">
                            <?php echo $difficulties[$ch['difficulty']]; ?>
                            <?php
                            $stars = ['easy' => '★★☆☆☆', 'medium' => '★★★☆☆', 'hard' => '★★★★☆', 'insane' => '★★★★★'];
                            echo ' ' . $stars[$ch['difficulty']];
                            ?>
                        </span>
                    </div>
                    <div class="challenge-icon"><?php echo $ch['icon'] ?? '🎯'; ?></div>
                </div>
                <div class="challenge-body">
                    <p class="challenge-desc"><?php echo htmlspecialchars($ch['description']); ?></p>
                    <div class="challenge-footer">
                        <div class="challenge-points">
                            <?php echo $ch['points']; ?> <span>points</span>
                        </div>
                        <div class="start-btn">Commencer →</div>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    <?php endif; ?>
</div>



<?php include 'includes/footer.php'; ?>