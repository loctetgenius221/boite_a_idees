<?php
session_start();

if(!isset($_SESSION['nom_collaborateur'])) {
    header('location:connexion.php');
}

/* Inclure le fichier config */
require_once "config.php";

/* Sélectionnez toutes les catégories */
$sql_categories = "SELECT id AS categorie_id, nom FROM categorie";
$result_categories = mysqli_query($bdd, $sql_categories);

/* Sélectionnez les idées en fonction de la catégorie sélectionnée (si une catégorie est choisie) */
$sql_ideas = "SELECT idee.*, categorie.nom AS categorie_nom FROM idee JOIN categorie ON idee.id_categorie = categorie.id";
if (isset($_GET['categorie']) && !empty($_GET['categorie'])) {
    $selected_categorie = $_GET['categorie'];
    $sql_ideas .= " WHERE categorie.id = $selected_categorie";
}

$result_ideas = mysqli_query($bdd, $sql_ideas);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/style.css">

    <style>
        .wrapper {
            width: 700px;
            margin: 0 auto;
        }

        .idea-card {
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
            text-decoration: underline;
            color: #007bff; /* Couleur du texte pour le nom de la catégorie */
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
            background-color: #007bff;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            margin-right: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease-in-out;
        }

        .edit-btn:hover, .delete-btn:hover {
            background-color: #0056b3;
        }

        /* Style pour le menu des catégories */
        label {
            font-weight: bold;
        }

        select {
            padding: 5px;
            border-radius: 5px;
            margin-right: 10px;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease-in-out;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
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
                        <h2 class="pull-left">Liste des idées</h2>
                        <form method="get" action="index.php">
                            <label for="categorie">Filtrer par catégorie:</label>
                            <select name="categorie" id="categorie">
                                <option value="">Toutes les catégories</option>
                                <?php
                                    $category_colors = []; // Tableau pour stocker les couleurs de catégorie

                                    while ($row_category = mysqli_fetch_assoc($result_categories)) {
                                        $category_color = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
                                        $category_colors[$row_category['categorie_id']] = $category_color;

                                        $selected = (isset($selected_categorie) && $selected_categorie == $row_category['categorie_id']) ? "selected" : "";
                                        echo "<option value='{$row_category['categorie_id']}' $selected>{$row_category['nom']}</option>";
                                    }
                                ?>
                            </select>
                            <input type="submit" value="Filtrer">
                        </form>
                    </div>
                    <div class="ideas-container">
                        <?php
                        if ($result_ideas) {
                            if (mysqli_num_rows($result_ideas) > 0) {
                                while ($row = mysqli_fetch_assoc($result_ideas)) {
                                    // Utiliser la couleur stockée pour la catégorie
                                    $category_color = isset($category_colors[$row['id_categorie']]) ? $category_colors[$row['id_categorie']] : '#bdd';

                                    echo '<div class="idea-card" style="border: 3px solid ' . $category_color . ';">';
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
                                    echo '<p><a href="read.php?id=' . $row['id'] . '" class="me-3" >Voir</a>';
                                    echo '</div>';
                                }
                                /* Free result set */
                                mysqli_free_result($result_ideas);
                            } else {
                                echo '<div class="alert alert-danger"><em>Pas d\'enregistrement</em></div>';
                            }
                        } else {
                            echo "Oops! Une erreur est survenue";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
