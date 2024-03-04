<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();



?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/style.css">

    <style>
        .wrapper{
            width: 700px;
            margin: 0 auto;
        }

        .idea-card {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
        }

        .idea-card h3 {
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

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fadeInUp {
            animation: fadeInUp 1s ease-out;
        }

        .wrapper {
            width: 700px;
            margin: 0 auto;
            animation: fadeInUp 1s ease-out;
        }

        /* Ajout de formes amusantes */
        .idea-card {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 15px; /* Bordure arrondie */
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
        }

        .idea-card:hover {
            transform: scale(1.05);
        }

        .idea-card h3 {
            color: #007bff;
            text-decoration: underline;
        }

        .me-3 {
            background-color: #4CAF50; /* Couleur de fond verte */
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 5px;
            margin-top: 15px;
        }

        /* Style pour les boutons d'édition et de suppression */
        .edit-btn, .delete-btn {
            padding: 10px 10px;
            margin-right: 5px;
            
            cursor: pointer;
            transition: background-color 0.3s ease-in-out;
        }

        
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h3>Hello, <span>Collaborateur</span> </h3>
            <h1><span><?php echo $_SESSION['nom_collaborateur'] ?></span></h1>
            <div class="head-nav">
                <a href="mes-idees.php" class="btn">Mes idées</a>
                <a href="deconnexion.php" class="btn">Déconnexion</a>
            </div>
        </div>
        <div class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="mt-5 mb-3 d-flex justify-content-between">
                        <a href="index.php" class="back_btn"><img src="images/back.png"> Retour</a>
                        <a href="create.php" class="Btn_add"><img src="images/plus.png"> Ajouter</a>
                        <h2 class="content-title">Liste de vos idées</h2>
                    </div>
                    <div class="ideas-container">
                        <?php 
                            /* Inclure le fichier config */
                            require_once "config.php";
                            // $user_id = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : null;
                            $user_id = $_SESSION['user_id'];
                            /* select query execution */
                            $sql = "SELECT idee.*, categorie.nom AS categorie_nom FROM idee JOIN categorie ON idee.id_categorie = categorie.id WHERE idee.id_utilisateur = $user_id ";

                            if ($stmt = mysqli_prepare($bdd, $sql)) {
                            
                                if (mysqli_stmt_execute($stmt)) {
                                    $result = mysqli_stmt_get_result($stmt);

                            
                                    // Reste du code pour traiter le résultat...
                                    if($result = mysqli_query($bdd, $sql)){
                                        if(mysqli_num_rows($result) > 0){
                                            while($row = mysqli_fetch_assoc($result)){
                                                echo '<div class="idea-card">';
                                                    echo '<h3>Categorie: ' . $row['categorie_nom'] . '</h3>';
                                                    echo '<p>titre: ' . $row['titre'] . '</p>';
                                                    $styleClass = '';
                                                    if ($row['statut'] === 'Approuvée') {
                                                        $styleClass = 'approuve';
                                                    } elseif ($row['statut'] === 'Refusée') {
                                                        $styleClass = 'refuse';
                                                    }
                    
                                                    echo '<p>statut: <span class="' . $styleClass . '"> ' . $row['statut'] . '</span></p>';
                                                    echo '<p>description: ' . $row['description'] . '</p>';
                                                    echo '<p>date: ' . $row['date_de_soumission'] . '</p>';
                                                    echo '<p><a href="read.php?id='. $row['id'] .'" class="me-3" >Voir</a>';
                                                    echo '<a href="update.php?id='. $row['id'] .'" class="edit-btn" ><img src="images/pen.png"></a>';
                                                    echo '<a href="delete.php?id='. $row['id'] .'" class="delete-btn" ><img src="images/trash.png" alt=""></a></p>';
                                                echo '</div>';
                                            }
                                            /* Free result set */
                                            mysqli_free_result($result);
                                            
                                        } else{
                                            echo '<div class="alert alert-danger"><em>Pas encore d\'idées proposées</em></div>';
                                        }
                                    } else{
                                        echo "Oops! Une erreur est survenue";
                                    }
                                } else {
                                    echo "Oops! Une erreur est survenue lors de l'exécution de la requête.";
                                }
                            
                                mysqli_stmt_close($stmt);
                            } else {
                                echo "Oops! Une erreur est survenue lors de la préparation de la requête.";
                            }
                            
                            
                           
                        
                            /* Fermer connection */
                            mysqli_close($bdd);
                        ?>
                    </div>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
