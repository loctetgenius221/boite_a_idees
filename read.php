<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



/* Verifiez si le paramettre id existe */
if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    
    require_once "config.php";
    
    /* Preparer la requete */
    $sql = "SELECT idee.*, categorie.nom AS categorie_nom FROM idee JOIN categorie ON idee.id_categorie = categorie.id WHERE idee.id = ?";
    
    if($stmt = mysqli_prepare($bdd, $sql)){
        
        mysqli_stmt_bind_param($stmt, "i", $param_id);
        
        
        $param_id = trim($_GET["id"]);
        
        
        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);
    
            if(mysqli_num_rows($result) == 1){
                /* recuperer l'enregistrement */
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                
                /* recuperer les champs */
                $categorie_nom = $row['categorie_nom']; // Corrected from $row['nom']
                $titre = $row["titre"];
                $description = $row["description"];
                $date_de_soumission = $row["date_de_soumission"];
                $statut = $row["statut"];


            } else{
                /* Si pas de id correct retourne la page d'erreur */
                header("location: error.php");
                exit();
            }
            
        } else{
            echo "Oops! une erreur est survenue.";
        }
    }
     
    
    mysqli_stmt_close($stmt);
    
    
    mysqli_close($bdd);
} else{
    /* Si pas de id correct retourne la page d'erreur */
    header("location: error.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Voir l'enregistrement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <style>
        .wrapper{
            width: 700px;
            margin: 0 auto;
        }

        .info-card {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
        }

        .info-card h3 {
            color: #007bff;
        }

        .approuve, .refuse {
            font-size: 18px;
            font-style: italic;
        }

        .approuve {
            color: #28a745; /* Vert */
        }

        .refuse {
            color: #dc3545; /* Rouge */
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="mt-5 mb-3">Voir l'idée</h1>
                    <div class="info-card">
                        <h3>Categorie: <?php echo $categorie_nom; ?></h3>
                        <p>titre: <?php echo $titre; ?></p>
                        <?php 
                        $styleClass = '';
                                            if ($row['statut'] === 'Approuvée') {
                                                $styleClass = 'approuve';
                                            } elseif ($row['statut'] === 'Refusée') {
                                                $styleClass = 'refuse';
                                            }
            
                        echo '<p>statut: <span class="' . $styleClass . '"> ' . $row['statut'] . '</span></p>';
                        ?>
                        <p>description: <?php echo $description; ?></p>
                        <p>date: <?php echo $date_de_soumission; ?></p>
                    </div>
                    <p><a href="index.php" class="btn btn-primary">Retour</a></p>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
