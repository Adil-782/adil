<?php
// D'abord la connexion, ENSUITE le header (pour que le fix du wallet fonctionne)
include 'includes/db_connect.php';
include 'includes/header.php';

$search = "";
$sql = "SELECT * FROM games";

// ---------------------------------------------------------
// FAILLE : SQL INJECTION (VULNÉRABILITÉ OBLIGATOIRE)
// ---------------------------------------------------------
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    // Pas de requête préparée ici = Faille SQLi
    $sql = "SELECT * FROM games WHERE title LIKE '%$search%'";
}
// ---------------------------------------------------------

$result = mysqli_query($conn, $sql);
?>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h2 style="color:white; border-bottom:1px solid #3a4b61; padding-bottom:10px; width:70%;">Jeux à la une</h2>

        <div class="search-area" style="width:28%;">
            <form method="GET" action="store.php" style="display:flex;">
                <input type="text" name="search" placeholder="recherche..." value="<?php echo htmlspecialchars($search); ?>" style="width:150px; margin-right:5px;">
                <button type="submit" style="background:none; border:none; cursor:pointer;">🔍</button>
            </form>
        </div>
    </div>

    <div class="games-list">
        <?php
        if (!$result) {
            echo "<p class='alert'>Erreur SQL : " . mysqli_error($conn) . "</p>";
        } else {
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    // Lien cliquable vers la page du jeu
                    echo "<a href='game.php?id=" . $row['id'] . "' class='game-row'>";

                    // -----------------------------------------------------
                    // GESTION INTELLIGENTE DES IMAGES
                    // -----------------------------------------------------
                    $imgSrc = $row['image_cover'];
                    $isUrl = filter_var($imgSrc, FILTER_VALIDATE_URL);

                    // Si ce n'est pas une URL, on regarde dans le dossier uploads
                    if (!$isUrl) {
                        if (!empty($imgSrc) && file_exists("uploads/".$imgSrc)) {
                            $imgSrc = "uploads/" . $imgSrc;
                        } else {
                            // Image par défaut si fichier introuvable
                            $imgSrc = "https://via.placeholder.com/120x45/333/ccc?text=NO+IMAGE";
                        }
                    }

                    // Affichage de l'image
                    echo "<img src='$imgSrc' class='game-img' style='width:120px; height:55px; object-fit:cover; margin-right:15px;'>";

                    // Info du jeu
                    echo "<div class='game-info'>";
                    echo "<div class='game-title'>" . htmlspecialchars($row['title']) . "</div>";

                    // Gestion du prix (Gratuit ou Montant)
                    $priceDisplay = ($row['price'] == 0) ? "Gratuit" : $row['price'] . " €";
                    echo "<div class='game-price'>" . $priceDisplay . "</div>";
                    echo "</div>";

                    echo "</a>";
                }
            } else {
                echo "<p style='padding:20px;'>Aucun jeu trouvé.</p>";
            }
        }
        ?>
    </div>

    <div style="margin-top: 40px; background: #000; padding: 20px; display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h3 style="color:#66c0f4; margin:0;">OFFRE DU WEEK-END</h3>
            <p style="margin:5px 0;">Économisez jusqu'à 0% sur nos jeux gratuits !</p>
        </div>
        <div class="btn-green" style="cursor:default;">En savoir plus</div>
    </div>

<?php include 'includes/footer.php'; ?>