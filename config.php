<?php
/* Database connexion */
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "boite_a_idee";

/* Connexion à la base de données */
$bdd = mysqli_connect($servername, $username, $password, $dbname);

/* verifier connection */
if($bdd === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>