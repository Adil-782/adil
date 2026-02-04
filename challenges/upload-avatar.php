<?php
session_start();

// Vérifier si connecté via le challenge 1
$isLoggedIn = isset($_SESSION['adil_logged']) && $_SESSION['adil_logged'] === true;

$message = "";
$messageType = "";
$uploadedFile = "";

// Créer le dossier uploads s'il n'existe pas
$uploadDir = __DIR__ . "/uploads/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Créer le fichier secret avec le flag
$secretDir = __DIR__ . "/../var/www/internal/";
if (!is_dir($secretDir)) {
    mkdir($secretDir, 0777, true);
}
$secretFile = $secretDir . "plans_deck2.txt";
if (!file_exists($secretFile)) {
    file_put_contents($secretFile, "
╔═══════════════════════════════════════════════════════════════════╗
║                     STEAM DECK 2 - CLASSIFIED                       ║
║                          INTERNAL USE ONLY                          ║
╠═══════════════════════════════════════════════════════════════════╣
║                                                                     ║
║  Project: Steam Deck 2 (Codename: APERTURE)                        ║
║  Status: Pre-Production                                             ║
║  Classification: TOP SECRET                                         ║
║                                                                     ║
║  Encryption Key for Technical Specifications:                       ║
║  ──────────────────────────────────────────                        ║
║                                                                     ║
║  🚩 FLAG: STEAM{RCE_V1A_AVATAR_UPLOAD_2024}                        ║
║                                                                     ║
║  This key grants access to the full Steam Deck 2 blueprints.       ║
║  Do NOT share outside of approved personnel.                        ║
║                                                                     ║
╚═══════════════════════════════════════════════════════════════════╝
");
}

// Traitement de l'upload - VULNÉRABLE
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['avatar'])) {
    $file = $_FILES['avatar'];
    $fileName = $file['name'];
    $fileTmp = $file['tmp_name'];
    $fileSize = $file['size'];

    // Vérification basique de l'extension - VULNÉRABLE à double extension
    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Liste des extensions interdites - MAIS BYPASS POSSIBLE !
    $forbidden = ['php', 'phtml', 'php3', 'php4', 'php5'];

    // FAILLE 1: Ne vérifie que l'extension finale
    // Un fichier "shell.php.jpg" passe !
    // Mais Apache peut être configuré pour exécuter .php.jpg ...

    // FAILLE 2: Ne vérifie pas le contenu réel du fichier
    // FAILLE 3: Pas de rename aléatoire, conserve le nom original

    // Vérification simpliste - BYPASSABLE
    $lastExtension = $extension;

    // On vérifie seulement que ça ne finit pas EXACTEMENT par .php
    // Mais .php5, .phar, .phtml peuvent fonctionner selon la config Apache
    if ($lastExtension === 'php') {
        $message = "❌ Les fichiers .php ne sont pas autorisés !";
        $messageType = "error";
    } else {
        // Upload sans vérification réelle du contenu
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($fileTmp, $targetPath)) {
            $message = "✅ Avatar uploadé avec succès !";
            $messageType = "success";
            $uploadedFile = "challenges/uploads/" . $fileName;
        } else {
            $message = "❌ Erreur lors de l'upload.";
            $messageType = "error";
        }
    }
}

// Lister les fichiers uploadés
$uploadedFiles = [];
if (is_dir($uploadDir)) {
    $files = scandir($uploadDir);
    foreach ($files as $f) {
        if ($f !== '.' && $f !== '..') {
            $uploadedFiles[] = $f;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Upload Avatar - Steam Deck 2 Server</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --bg-dark: #0c1018;
            --bg-card: #161d2a;
            --accent: #00d4aa;
            --accent-dark: #00a080;
            --text: #e8e8e8;
            --text-muted: #7a8599;
            --danger: #ff4757;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-dark);
            min-height: 100vh;
            color: var(--text);
            background-image:
                radial-gradient(ellipse at 30% 0%, rgba(0, 212, 170, 0.08) 0%, transparent 50%),
                radial-gradient(ellipse at 70% 100%, rgba(0, 212, 170, 0.05) 0%, transparent 50%);
        }

        .header {
            background: linear-gradient(180deg, rgba(22, 29, 42, 0.98) 0%, transparent 100%);
            padding: 20px 40px;
            border-bottom: 1px solid rgba(0, 212, 170, 0.15);
        }

        .header-content {
            max-width: 1000px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo h1 {
            font-size: 20px;
            font-weight: 700;
            color: var(--accent);
        }

        .logo span {
            font-size: 10px;
            background: rgba(255, 71, 87, 0.2);
            color: var(--danger);
            padding: 4px 10px;
            border-radius: 4px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .user-badge {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(0, 0, 0, 0.3);
            padding: 8px 16px;
            border-radius: 8px;
        }

        .user-badge .avatar {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .page-title {
            margin-bottom: 30px;
        }

        .page-title h2 {
            font-size: 28px;
            margin-bottom: 8px;
        }

        .page-title p {
            color: var(--text-muted);
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .card {
            background: var(--bg-card);
            border-radius: 16px;
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .card h3 {
            font-size: 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert {
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: rgba(0, 212, 170, 0.15);
            border: 1px solid rgba(0, 212, 170, 0.3);
            color: var(--accent);
        }

        .alert-error {
            background: rgba(255, 71, 87, 0.15);
            border: 1px solid rgba(255, 71, 87, 0.3);
            color: var(--danger);
        }

        .upload-area {
            border: 2px dashed rgba(0, 212, 170, 0.3);
            border-radius: 12px;
            padding: 40px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .upload-area:hover {
            border-color: var(--accent);
            background: rgba(0, 212, 170, 0.05);
        }

        .upload-area .icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .upload-area p {
            color: var(--text-muted);
            font-size: 13px;
        }

        .upload-area input[type="file"] {
            display: none;
        }

        .btn {
            padding: 14px 28px;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
            color: white;
            width: 100%;
            margin-top: 20px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 212, 170, 0.3);
        }

        .file-list {
            list-style: none;
        }

        .file-list li {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 15px;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            margin-bottom: 8px;
            font-size: 13px;
        }

        .file-list li a {
            color: var(--accent);
            text-decoration: none;
        }

        .file-list li a:hover {
            text-decoration: underline;
        }

        .hint-box {
            background: rgba(255, 193, 7, 0.1);
            border: 1px solid rgba(255, 193, 7, 0.3);
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
        }

        .hint-box h4 {
            color: #ffc107;
            font-size: 12px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .hint-box p {
            color: var(--text-muted);
            font-size: 12px;
            line-height: 1.6;
        }

        .hint-box code {
            background: rgba(0, 0, 0, 0.3);
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 11px;
            color: var(--accent);
        }

        .warning-banner {
            background: linear-gradient(135deg, rgba(255, 71, 87, 0.2) 0%, rgba(255, 71, 87, 0.05) 100%);
            border: 1px solid rgba(255, 71, 87, 0.3);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .warning-banner .icon {
            font-size: 32px;
        }

        .warning-banner h4 {
            color: var(--danger);
            margin-bottom: 5px;
        }

        .warning-banner p {
            color: var(--text-muted);
            font-size: 13px;
        }

        .back-link {
            display: inline-block;
            margin-top: 30px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 13px;
        }

        .back-link:hover {
            color: var(--accent);
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="header-content">
            <div class="logo">
                <h1>🎮 Steam Deck 2 Server</h1>
                <span>PRE-PRODUCTION</span>
            </div>
            <div class="user-badge">
                <div class="avatar">👤</div>
                <div>
                    <div style="font-weight: 600; font-size: 13px;">adil_dev</div>
                    <div style="font-size: 10px; color: var(--text-muted);">Employee</div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="page-title">
            <h2>📸 Modifier l'Avatar du Profil</h2>
            <p>Uploadez une nouvelle image pour votre profil employé.</p>
        </div>

        <div class="warning-banner">
            <div class="icon">⚠️</div>
            <div>
                <h4>Serveur de Pré-Production</h4>
                <p>Ce serveur contient des données sensibles. Les fichiers uploadés sont stockés dans
                    <code>/challenges/uploads/</code></p>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo $message; ?>
                <?php if ($uploadedFile): ?>
                    <br>📁 Fichier accessible : <a href="../<?php echo $uploadedFile; ?>" target="_blank"
                        style="color: inherit;">/
                        <?php echo $uploadedFile; ?>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="grid">
            <div class="card">
                <h3>📤 Upload d'Avatar</h3>

                <form method="POST" enctype="multipart/form-data">
                    <label class="upload-area" for="avatarInput">
                        <div class="icon">🖼️</div>
                        <p>Cliquez ou glissez une image ici</p>
                        <p style="margin-top: 8px; font-size: 11px;">Formats acceptés : JPG, PNG, GIF</p>
                        <input type="file" name="avatar" id="avatarInput" required>
                    </label>
                    <div id="fileName"
                        style="text-align: center; margin-top: 10px; color: var(--accent); font-size: 13px;"></div>
                    <button type="submit" class="btn btn-primary">Uploader l'Avatar</button>
                </form>

                <div class="hint-box">
                    <h4>💡 Note de sécurité</h4>
                    <p>Le serveur bloque les fichiers <code>.php</code> pour des raisons de sécurité. Seules les images
                        sont autorisées...</p>
                    <p style="margin-top: 8px;">Le fichier secret se trouve dans :
                        <code>/var/www/internal/plans_deck2.txt</code></p>
                </div>
            </div>

            <div class="card">
                <h3>📁 Fichiers Uploadés</h3>

                <?php if (empty($uploadedFiles)): ?>
                    <p style="color: var(--text-muted); text-align: center; padding: 30px;">Aucun fichier uploadé.</p>
                <?php else: ?>
                    <ul class="file-list">
                        <?php foreach ($uploadedFiles as $f): ?>
                            <li>
                                <span>📄
                                    <?php echo htmlspecialchars($f); ?>
                                </span>
                                <a href="uploads/<?php echo htmlspecialchars($f); ?>" target="_blank">Ouvrir →</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <a href="../challenges.php" class="back-link">← Retour aux challenges</a>
    </div>

    <script>
        document.getElementById('avatarInput').addEventListener('change', function (e) {
            if (e.target.files[0]) {
                document.getElementById('fileName').textContent = '📎 ' + e.target.files[0].name;
            }
        });
    </script>
</body>

</html>