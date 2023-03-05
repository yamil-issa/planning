<?php
require '../vendor/autoload.php';
//connexion
try {
    $manager = new MongoDB\Driver\Manager("mongodb+srv://Yamil:leviathan@cluster0.euhnuka.mongodb.net/test?retryWrites=true&w=majority&ssl=true");
} catch (MongoDB\Driver\Exception\Exception $e) {
    echo "Probleme! : ".$e->getMessage();
    exit();
}

?>

