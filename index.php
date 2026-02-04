<?php
// Inclusion connexion
if (file_exists('includes/db_connect.php')) {
    include 'includes/db_connect.php';
} else {
    include 'db_connect.php';
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $pass = $_POST['password'];
    $pass_hash = md5($pass);

    // MODIF ICI : On sélectionne aussi 'avatar'
    $sql = "SELECT id, username, role, wallet_balance, avatar FROM users WHERE username = '$user' AND password = '$pass_hash'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        session_start();
        $row = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role'];
        $_SESSION['wallet'] = $row['wallet_balance'];

        // MODIF ICI : On stocke l'avatar en session
        $_SESSION['avatar'] = $row['avatar'];

        header("Location: store.php");
        exit();
    } else {
        $message = "Identifiants incorrects.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Chtim</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Motiva+Sans:wght@400;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="global-header">
    <div class="header-content">
        <div class="logo"><h1><a href="index.php"><span style="color:#fff;">CH</span>TIM</a></h1></div>
    </div>
</div>
<div class="login-wrapper">
    <div class="login-left">
        <div class="login-title">CONNEXION</div>
        <?php if($message): ?>
            <div style="background-color: #c94f4f; color: white; padding: 10px; margin-bottom: 15px;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="steam-input-group">
                <label>Nom de compte</label>
                <input type="text" name="username" class="steam-input" required>
            </div>
            <div class="steam-input-group">
                <label>Mot de passe</label>
                <input type="password" name="password" class="steam-input" required>
            </div>
            <button type="submit" class="btn-steam-login">Se connecter</button>
        </form>
    </div>
    <div class="login-right">
        <div style="margin-bottom: 40px;">
            <p style="color:#1999ff; font-size:12px;">CONNEXION VIA QR</p>
            <div class="qr-placeholder"><div style="width:130px; height:130px; background: radial-gradient(#000 30%, transparent 31%); background-size: 10px 10px; opacity: 0.8;"></div></div>
        </div>
        <div style="border-top: 1px solid #333; padding-top: 20px;">
            <p style="color:#b8b6b4; font-size:12px;">Nouveau sur Chtim ?</p>
            <a href="register.php" style="display: block; border: 1px solid rgba(255,255,255,0.4); color: white; padding: 8px; text-align: center;">Créer un compte</a>
        </div>
    </div>
</div>
</body>
</html>