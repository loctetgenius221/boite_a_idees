<?php
    @include 'config.php';

    if(isset($_POST['inscription'])) {

        $nom = mysqli_real_escape_string($bdd, $_POST['nom']);
        $prenom = mysqli_real_escape_string($bdd, $_POST['prenom']);
        $email = mysqli_real_escape_string($bdd, $_POST['email']);
        $pass = ($_POST['mot_de_pass']);
        $cpass = ($_POST['cmot_de_pass']);
        $statut = $_POST['statut_utilisateur'];

        $select = "SELECT * FROM utilisateurs WHERE email = '$email'";

        $result = mysqli_query($bdd, $select);

        if(mysqli_num_rows($result) > 0) {
            $error[] = 'Ce nom d\'utilisateur existe déjà.';
        }
        else {
            if($pass != $cpass) {
                $error[] = 'Le mot de passe est incorrect!';
            }
            else {
                $insert = "INSERT INTO utilisateurs(nom, prenom, email, mot_de_pass, statut) VALUES ('$nom', '$prenom', '$email', '$pass', '$statut_utilisateur')";
                mysqli_query($bdd, $insert);
                header('location: connexion.php');
                exit(); // Ajout d'une sortie pour éviter l'exécution du code suivant même après la redirection.
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page d'inscription</title>
    <!-- lien du style css -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="formulaire">
        <form action="" method="POST">
            <h3>S'inscrire maintenant</h3>
            <?php
                if(isset($error)) {
                    foreach($error as $error_msg) {
                        echo '<span class="error-msg">'.$error_msg.'</span>';
                    }
                }
            ?>
            <input type="text" name="nom" required placeholder="Entrez votre nom..">
            <input type="text" name="prenom" required placeholder="Entrez votre prénom..">
            <input type="email" name="email" required placeholder="Entrez votre email..">
            <input type="password" name="mot_de_pass" required placeholder="Entrez votre mot de passe..">
            <input type="password" name="cmot_de_pass" required placeholder="Confirmez votre mot de passe..">
            <select name="statut_utilisateur">
                <option value="Approbateur">Approbateur</option>
                <option value="Collaborateur">Collaborateur</option>
            </select>
            <input type="submit" name="inscription" value="S'inscrire maintenant" class="form-btn">
            <p>Vous avez déjà un compte? <a href="connexion.php">Se connecter</a></p>
        </form>
    </div>
</body>
</html>
