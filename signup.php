<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CATALOGUE - Inscription</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="formulaire">
        <h2>Créer un compte</h2>


<?php
if (!empty($_SESSION['id'])) {
    header('Location: index.php');
    exit();
}





        if(!empty($_SESSION['erreur'])): ?>
            <p class=\"message-erreur\"><?php echo $_SESSION['erreur']; ?></p>
            <?php unset($_SESSION['erreur']); ?>
        <?php endif; ?>

        <form action="actions/action_signup.php" method="POST">
            <div class="champ">
                <label>Nom complet :</label>
                <input type="text" name="nom" placeholder="The Rock">
            </div>

            <div class="champ">
                <label>Numéro de téléphone (8 chiffres) :</label>
                <input type="tel" name="telephone" pattern="[0-9]{8}" maxlength="8" placeholder="12345678" required>
            </div>

            <div class="champ">
                <label>Mot de passe :</label>
                <input type="password" name="mdp" placeholder="Choisissez un mot de passe">
            </div>

            <button type="submit">S'inscrire</button>
        </form>

        <div class="lien-bas">
            Déjà inscrit ? <a href="login.php">Connectez-vous ici</a>
        </div>
    </div>

</body>
</html>