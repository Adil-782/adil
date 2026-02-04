<?php
if (file_exists('includes/db_connect.php')) {
    include 'includes/db_connect.php';
    include 'includes/header.php';
} else {
    include 'db_connect.php';
    include 'header.php';
}

if (!isset($_GET['id'])) {
    if (isset($_SESSION['user_id']))
        header("Location: profile.php?id=" . $_SESSION['user_id']);
    else
        header("Location: index.php");
    exit();
}

$pid = intval($_GET['id']);
$sql = "SELECT * FROM users WHERE id = $pid";
$res = mysqli_query($conn, $sql);

if (mysqli_num_rows($res) == 0)
    die("<div class='container'><p>Utilisateur inconnu.</p></div>");
$u = mysqli_fetch_assoc($res);
?>

<div class="container" style="margin-top: 40px;">
    <div class="card" style="background:#1b2838; padding:20px; display:flex; gap:20px;">
        <div style="width: 166px; height: 166px; border: 2px solid #57cbde; padding: 2px;">
            <img src="<?php echo $u['avatar']; ?>" style="width: 100%; height: 100%; object-fit: cover;">
        </div>

        <div>
            <h1 style="color:white; margin-top:0;"><?php echo htmlspecialchars($u['username']); ?></h1>
            <p style="color:#8f98a0;">Niveau 13</p>
            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $pid): ?>
                <div style="background:rgba(0,0,0,0.3); padding:10px; margin-top:10px;">
                    Email: <?php echo htmlspecialchars($u['email']); ?><br>
                    Solde: <span style="color:#a4d007;"><?php echo $u['wallet_balance']; ?> €</span>
                </div>
                <div style="margin-top:15px;">
                    <a href="change-password.php"
                        style="display:inline-block; padding:10px 20px; background:linear-gradient(135deg, var(--accent-blue-light), var(--accent-blue-dark)); color:white; text-decoration:none; border-radius:5px; font-weight:600; font-size:13px; transition: var(--transition-normal);">
                        🔒 Changer le mot de passe
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <h3 style="color:white; border-bottom:1px solid #3a4b61; margin-top:40px;">Jeux possédés</h3>
    <div class="games-list">
        <?php
        $sql_lib = "SELECT g.title, g.image_cover FROM library l JOIN games g ON l.game_id = g.id WHERE l.user_id = $pid";
        $res_lib = mysqli_query($conn, $sql_lib);
        while ($g = mysqli_fetch_assoc($res_lib)) {
            $img = filter_var($g['image_cover'], FILTER_VALIDATE_URL) ? $g['image_cover'] : "uploads/" . $g['image_cover'];
            echo "<div class='game-row' style='cursor:default;'>
                    <img src='$img' style='width:120px; height:55px; object-fit:cover; margin-right:15px;'>
                    <div class='game-title'>" . htmlspecialchars($g['title']) . "</div>
                  </div>";
        }
        ?>
    </div>
</div>
<?php if (file_exists('includes/footer.php'))
    include 'includes/footer.php';
else
    include 'footer.php'; ?>