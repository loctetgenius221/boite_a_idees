<?php
require_once "config.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approuver']) || isset($_POST['refuser'])) {
        $idee_id = $_POST['idee_id'];
        $nouveau_statut = isset($_POST['approuver']) ? 'Approuvée' : 'Refusée';

        $updateQuery = "UPDATE idee SET statut = ? WHERE id = ?";
        $stmt = mysqli_prepare($bdd, $updateQuery);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'si', $nouveau_statut, $idee_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }
}
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
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
        }

        .idea-card h3 {
            color: #007bff;
        }

        .approuve {
            color: green;
            font-size: 18px;
            font-style: italic;
        }

        .refuse {
            color: red;
            font-size: 18px;
            font-style: italic;
        }

        button {
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

        button:hover {
            background-color: #45a049; /* Couleur de fond verte foncée au survol */
        }

        .refuse-button {
            background-color: #f44336; /* Couleur de fond rouge pour le bouton de refus */
        }

        .refuse-button:hover {
            background-color: #d32f2f; /* Couleur de fond rouge foncé au survol */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h3>Hello, <span>Approbateur</span> </h3>
            <h1><span><?php echo $_SESSION['nom_approbateur'] ?></span></h1>
            <div class="head-nav">
                <a href="deconnexion.php" class="btn">Déconnexion</a>
            </div>
        </div>
        <div class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="mt-5 mb-3 d-flex justify-content-between">
                        <h2 class="pull-left">Liste des idées</h2>
                    </div>
                    <div class="ideas-container">
                        <?php
                        $sql = "SELECT idee.*, categorie.nom AS categorie_nom FROM idee JOIN categorie ON idee.id_categorie = categorie.id";
                        $result = mysqli_query($bdd, $sql);

                        if ($result) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<div class="idea-card">';
                                echo '<h3>Categorie: ' . $row['categorie_nom'] . '</h3>';
                                if ($row['statut'] === 'Approuvée') {
                                    $styleClass = 'approuve';
                                } elseif ($row['statut'] === 'Refusée') {
                                    $styleClass = 'refuse';
                                }

                                echo '<p>statut: <span class="' . $styleClass . '"> ' . $row['statut'] . '</span></p>';

                                echo '<p>titre: ' . $row['titre'] . '</p>';
                                echo '<p>description: ' . $row['description'] . '</p>';
                                echo '<p>date: ' . $row['date_de_soumission'] . '</p>';
                                $styleClass = '';
                                
                                echo '<form method="post">';
                                echo '<input type="hidden" name="idee_id" value="' . $row['id'] . '">';
                                echo '<button type="submit" name="approuver">Approuver</button>';
                                echo '<button type="submit" name="refuser" class="refuse-button">Refuser</button>';
                                echo '</form>';
                                echo '</div>';
                            }
                            mysqli_free_result($result);
                        } else {
                            echo "Oops! Une erreur est survenue";
                        }

                        mysqli_close($bdd);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
