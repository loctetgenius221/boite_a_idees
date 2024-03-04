<?php
// connexion.php
// require_once "config.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();



?>

<?php
    @include 'config.php';


    if(isset($_POST['connexion'])) {

        $email = mysqli_real_escape_string($bdd, $_POST['email']);
        $pass = ($_POST['mot_de_pass']);

        $select =  " SELECT * FROM utilisateurs WHERE email = '$email' AND mot_de_pass = '$pass' ";

        $result = mysqli_query($bdd, $select);

        if(mysqli_num_rows($result) > 0) {

            $row = mysqli_fetch_array($result);
            // var_dump($row);

            if($row['statut'] == 'Approbateur') {
                $_SESSION['nom_approbateur'] = $row['nom']. ' ' . $row['prenom'];
                $_SESSION['user_id'] = $row['id'];
                header('location: adminpage.php');
                exit(); // Ajout d'une sortie pour éviter l'exécution du code suivant même après la redirection.
            } elseif($row['statut'] == 'Collaborateur') {
                $_SESSION['nom_collaborateur'] = $row['nom']. ' ' . $row['prenom'];
                $_SESSION['user_id'] = $row['id'];
                header('location: index.php');
                exit(); // Ajout d'une sortie pour éviter l'exécution du code suivant même après la redirection.
            }

        }else {
            $error[] = 'Email ou mot de passe incorrect!';
        }
    }
    
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page de connexion</title>
    <!-- lien du style.css -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    
    <div class="formulaire">
        <form action="" method="POST">
            <h3>Se connecter maintenant</h3>
            <?php
                if(isset($error)) {
                    foreach($error as $error_msg) {
                        echo '<span class="error-msg">'.$error_msg.'</span>';
                    }
                }
            ?>
            <input type="email" name="email" required placeholder="Entrez votre email..">
            <input type="password" name="mot_de_pass" required placeholder="Entrez votre mot de passe..">
            <input type="submit" name="connexion" value="Se connecter maintenant" class="form-btn">
            <p>Vous n'avez pas de compte? <a href="inscription.php">S'inscrire</a></p>
        </form>
    </div>
</body>
</html>
