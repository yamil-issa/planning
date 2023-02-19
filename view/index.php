<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/css/style.css">
    <title>planning</title>
</head>
<body>
    <h1>
        Planning
    </h1>
</body>
</html>



<?php
require '../model/connection.php';

try {
 $filter = [];
 $option = [];
 $read = new MongoDB\Driver\Query($filter, $option);

 $query = new MongoDB\Driver\Query([], ['limit' => 52]);
 //Exécution de la requête
 $cursor = $manager->executeQuery('planning.users', $read);
 $line = $manager->executeQuery('planning.dates', $query);

}catch (MongoDB\Driver\Exception\Exception $e) {
    echo "Probleme! : ".$e->getMessage();
 exit();

}


?>

<select class="form-select" name="type" id="ty">
<option value="nobody">personne</option>
<?php
        foreach ($cursor as $user) { 
            $user_name = $user->nom;
            $id_user = $user->_id;
        ?>
    <option value='<?= $id_user ?>'><?= $user_name ?></option>
    <?php } ?>
</select>


<?php


foreach ($line as $document) {
    $timestamp = strtotime($document->start_date);
    $date = date('d/m/Y', $timestamp);

    echo '<ul>
    <li>'.$date.' </li>
    </ul>';
}

//echo '<p>Statistiques</p>'

?>
