<?php
// Page de test simple pour vérifier la mise à jour
session_start();
include 'includes/db_connect.php';

echo "<h2>🧪 Test de mise à jour des points CTF</h2>";

// Afficher l'ID utilisateur de la session
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    echo "<p>✅ Session active</p>";
    echo "<p>User ID: <strong>$userId</strong></p>";
    echo "<p>Username: <strong>" . $_SESSION['username'] . "</strong></p>";

    // Obtenir les points actuels
    $current = mysqli_fetch_assoc(mysqli_query($conn, "SELECT ctf_points FROM users WHERE id = $userId"));
    echo "<p>Points actuels: <strong>" . $current['ctf_points'] . "</strong> pts</p>";

    // Tester l'ajout de 100 points (comme un flag)
    if (isset($_GET['test'])) {
        echo "<hr><h3>Test d'ajout de 100 points...</h3>";
        $points = 100;
        $updateQuery = "UPDATE users SET ctf_points = ctf_points + $points WHERE id = $userId";
        echo "<p>Requête: <code>$updateQuery</code></p>";

        $result = mysqli_query($conn, $updateQuery);

        if ($result) {
            $affected = mysqli_affected_rows($conn);
            echo "<p style='color: green;'>✅ Succès ! $affected ligne(s) affectée(s)</p>";

            // Relire les points
            $new = mysqli_fetch_assoc(mysqli_query($conn, "SELECT ctf_points FROM users WHERE id = $userId"));
            echo "<p>Nouveaux points: <strong>" . $new['ctf_points'] . "</strong> pts</p>";
            echo "<p><a href='test_simple.php'>Rafraîchir</a></p>";
        } else {
            echo "<p style='color: red;'>❌ Erreur: " . mysqli_error($conn) . "</p>";
        }
    } else {
        echo "<p><a href='?test=1' style='display: inline-block; padding: 10px 20px; background: #1dd1a1; color: white; text-decoration: none; border-radius: 5px;'>🧪 Tester l'ajout de 100 points</a></p>";
    }

} else {
    echo "<p style='color: red;'>❌ Aucune session active. Veuillez vous connecter d'abord.</p>";
    echo "<p><a href='index.php'>Se connecter</a></p>";
}

echo "<hr>";
echo "<p><a href='submit-flag.php'>← Soumettre un flag</a> | <a href='profile.php'>Profil</a></p>";
?>