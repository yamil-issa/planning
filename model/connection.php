<?php

//connexion à
try {
    $manager = new MongoDB\Driver\Manager("mongodb+srv://Yamil:leviathan@cluster0.euhnuka.mongodb.net/test");
} catch (MongoDB\Driver\Exception\Exception $e) {
    echo "Probleme! : ".$e->getMessage();
    exit();
}


?>

