<?php
/* Inclure le fichier config */
require_once "config.php";
// Assurez-vous que vous avez démarré la session
session_start();
$user_id = $_SESSION['user_id'];

/* Définir les variables */
$titre = $description = $categorie_id = "";
$titre_err = $description_err = $categorie_id_err = "";

// Récupérer la liste des catégories depuis la base de données
$categorie_list = array();
$sql_categories = "SELECT id, nom FROM categorie";
$result_categories = mysqli_query($bdd, $sql_categories);

if ($result_categories) {
    while ($row = mysqli_fetch_assoc($result_categories)) {
        $categorie_list[$row['id']] = $row['nom'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    /* Valider le titre */
    $input_titre = trim($_POST["titre"]);
    if (empty($input_titre)) {
        $titre_err = "Veuillez entrer un titre.";
    } else {
        $titre = $input_titre;
    }

    /* Valider la description */
    $input_description = trim($_POST["description"]);
    if (empty($input_description)) {
        $description_err = "Veuillez entrer une description.";
    } else {
        $description = $input_description;
    }

    /* Valider la catégorie */
    $input_categorie_id = trim($_POST["categorie_id"]);
    if (empty($input_categorie_id)) {
        $categorie_id_err = "Veuillez choisir une catégorie.";
    } else {
        $categorie_id = $input_categorie_id;
    }

    /* Vérifier les erreurs avant l'enregistrement */
    if (empty($titre_err) && empty($description_err) && empty($categorie_id_err)) {
        $sql = "INSERT INTO idee (titre, description, id_categorie, id_utilisateur) VALUES (?, ?, ?, $user_id)";

        if ($stmt = mysqli_prepare($bdd, $sql)) {
            /* Lier les variables à la requête préparée */
            mysqli_stmt_bind_param($stmt, "ssi", $param_titre, $param_description, $param_categorie_id);

            /* Définir les paramètres */
            $param_titre = $titre;
            $param_description = $description;
            $param_categorie_id = $categorie_id;

            /* Exécuter la requête */
            if (mysqli_stmt_execute($stmt)) {
                /* Opération effectuée, retour */
                header("location: index.php");
                exit();
            } else {
                echo "Oops! Une erreur est survenue.";
            }
        }


        /* Fermer la déclaration */
        mysqli_stmt_close($stmt);
    }

    /* Fermer la connexion */
    mysqli_close($bdd);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Record</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <style>
        .wrapper{
            width: 700px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mt-5">Créer un enregistrement</h2>
                    <p>Remplir le formulaire pour enregistrer l'idée dans la base de données</p>

                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group">
                            <label>Titre</label>
                            <input type="text" name="titre" class="form-control <?php echo (!empty($titre_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $titre; ?>">
                            <span class="invalid-feedback"><?php echo $titre_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control <?php echo (!empty($description_err)) ? 'is-invalid' : ''; ?>"><?php echo $description; ?></textarea>
                            <span class="invalid-feedback"><?php echo $description_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Catégorie</label>
                            <select name="categorie_id" class="form-control <?php echo (!empty($categorie_id_err)) ? 'is-invalid' : ''; ?>">
                                <option value="" selected disabled>Choisir une catégorie</option>
                                <?php foreach ($categorie_list as $id => $nom) : ?>
                                    <option value="<?php echo $id; ?>"><?php echo $nom; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="invalid-feedback"><?php echo $categorie_id_err;?></span>
                        </div>
                        <input type="submit" class="btn btn-primary" value="Enregistrer">
                        <a href="mes-idees.php" class="btn btn-secondary ml-2">Annuler</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>


