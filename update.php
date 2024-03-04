<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/* Inclure le fichier de configuration */
require_once "config.php";

/* Définir les variables */
$id = $titre = $description = $categorie_nom = "";
$titre_err = $description_err = $categorie_nom_err = "";

/* Récupérer les catégories existantes depuis la base de données */
$categories = array();
$sql_categories = "SELECT id, nom FROM categorie";
$result_categories = mysqli_query($bdd, $sql_categories);
while ($row = mysqli_fetch_assoc($result_categories)) {
    $categories[$row['id']] = $row['nom'];
}

/* Vérifier si le formulaire a été soumis */
if($_SERVER["REQUEST_METHOD"] == "POST") {
    /* Récupérer la valeur de l'identifiant caché */
    $id = $_POST["id"];

    /* Valider le titre */
    $input_titre = trim($_POST["titre"]);
    if(empty($input_titre)){
        $titre_err = "Veuillez entrer un titre.";
    } else {
        $titre = $input_titre;
    }

    /* Valider la description */
    $input_description = trim($_POST["description"]);
    if(empty($input_description)){
        $description_err = "Veuillez entrer une description.";
    } else {
        $description = $input_description;
    }

    /* Valider la catégorie */
    $input_categorie_nom = $_POST["categorie_nom"];
    if(empty($input_categorie_nom) || !array_key_exists($input_categorie_nom, $categories)){
        $categorie_nom_err = "Veuillez sélectionner une catégorie valide.";
    } else {
        $categorie_nom = $input_categorie_nom;
    }

    /* Vérifier s'il n'y a pas d'erreurs avant la mise à jour */
    if(empty($titre_err) && empty($description_err) && empty($categorie_nom_err)) {
        $sql = "UPDATE idee SET titre=?, description=?, id_categorie=? WHERE id=?";
        
        if($stmt = mysqli_prepare($bdd, $sql)){
            mysqli_stmt_bind_param($stmt, "ssii", $param_titre, $param_description, $param_categorie_nom, $param_id);

            $param_titre = $titre;
            $param_description = $description;
            $param_categorie_nom = $categorie_nom;
            $param_id = $id;

            if(mysqli_stmt_execute($stmt)){
                /* Enregistrement modifié, retour */
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
} else {
    /* S'il existe un paramètre d'ID valide dans l'URL */
    if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
        $id = trim($_GET["id"]);

        $sql = "SELECT idee.*, categorie.nom AS categorie_nom 
                FROM idee 
                JOIN categorie ON idee.id_categorie = categorie.id 
                WHERE idee.id = ?";

        if($stmt = mysqli_prepare($bdd, $sql)){
            mysqli_stmt_bind_param($stmt, "i", $param_id);

            $param_id = $id;

            if(mysqli_stmt_execute($stmt)){
                $result = mysqli_stmt_get_result($stmt);

                if(mysqli_num_rows($result) == 1){
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

                    $titre = $row["titre"];
                    $description = $row["description"];
                    $categorie_nom = $row["categorie_nom"];
                } else{
                    header("location: error.php");
                    exit();
                }
            } else {
                echo "Oops! Une erreur est survenue.";
            }
        }

        /* Fermer la déclaration */
        mysqli_stmt_close($stmt);

        /* Fermer la connexion */
        mysqli_close($bdd);
    } else {
        /* Si aucun paramètre d'ID valide n'est fourni, rediriger vers une page d'erreur */
        header("location: error.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier l'enregistrement</title>
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
                    <h2 class="mt-5">Mise à jour de l'enregistrement</h2>
                    <p>Modifier le titre, la description et la catégorie, puis enregistrer</p>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
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
                            <select name="categorie_nom" class="form-control <?php echo (!empty($categorie_nom_err)) ? 'is-invalid' : ''; ?>">
                                <?php
                                    foreach($categories as $key => $value){
                                        echo "<option value='" . $key . "'".(($categorie_nom==$value)?'selected="selected"':"").">" . $value . "</option>";
                                    }
                                ?>
                            </select>
                            <span class="invalid-feedback"><?php echo $categorie_nom_err;?></span>
                        </div>
                        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Enregistrer">
                        <a href="mes-idees.php" class="btn btn-secondary ml-2">Annuler</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
