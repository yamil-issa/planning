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
 //Exécution des requêtes
 $cursor = $manager->executeQuery('planning.users', $read);
 $line = $manager->executeQuery('planning.dates', $query);

}catch (MongoDB\Driver\Exception\Exception $e) {
    echo "Probleme! : ".$e->getMessage();
 exit();

}

$users_tab = [];

   foreach ($cursor as $user) { 
            $users_tab[] = $user;
   
} 


?>



<!--<select class="form-select" name="type" id="ty">
<option value="nobody">personne</option>
<option value='<?= $user_id ?>'><?= $user_name ?></option>
</select> !-->

<?php

$options = array_map(function ($users_tab) {
    return "<option value=\"$users_tab->_id\">$users_tab->nom</option>";
}, $users_tab);
$select = "<select>" . implode("", $options) . "</select>";


foreach ($line as $document) {
    $timestamp = strtotime($document->start_date);
    $date = date('d/m/Y', $timestamp);
    ?>

    <ul>
     <li><?=$date ?>&nbsp;<?=$select ?></li>
    </ul>
<?php } ?>

