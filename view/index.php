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
    <script src="../js/app.js"></script>
</body>
</html>



<?php
require '../model/connection.php';



try {
    $filter = [];
    $option = [];


    if (isset($_POST['selectedYear'])) {
        $selected_year = $_POST['selectedYear'];
    } else {
        $selected_year = "2023";
    };



    $dateFilter = [
        'year' => $selected_year,
        'start_date' => [
            '$gte' => "{$selected_year}-01-01",
            '$lte' => "{$selected_year}-12-31",
        ],
    ];



    $read = new MongoDB\Driver\Query($filter, $option);

    $query = new MongoDB\Driver\Query($dateFilter, ['limit' => 365]);
    //Exécution des requêtes
    $cursor = $manager->executeQuery('planning.users', $read);
    $line = $manager->executeQuery('planning.dates', $query)->toArray();;
} catch (MongoDB\Driver\Exception\Exception $e) {
    echo "Probleme! : ".$e->getMessage();
    exit();
}

$users_tab = [];
foreach ($cursor as $user) {
    $user->dates = isset($user->dates) ? $user->dates : []; // add dates array if it doesn't exist
    $users_tab[] = $user;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit-btn'])) {
    // get dates and corresponding user ids from $_POST['users']
    $users = $_POST['users'];

    // create BulkWrite instance for updating user documents
    $bulkWrite = new MongoDB\Driver\BulkWrite;

    // iterate over each user in $_POST['users']
    foreach ($users as $date => $userId) {
        if ($userId === 'personne') {
            continue;
        }
        // retrieve user document to check if the date is already assigned
        $userFilter = ['_id' => new MongoDB\BSON\ObjectId($userId)];
        $userQuery = new MongoDB\Driver\Query($userFilter);
        $userDocument = current($manager->executeQuery('planning.users', $userQuery)->toArray());

        // if the user already has the date assigned, don't do anything
        if (in_array($date, $userDocument->dates)) {
            continue;
        }

        // remove the date from any other user that currently has it assigned
        $updateQuery = ['$pull' => ['dates' => $date]];
        $bulkWrite->update(
            ['dates' => $date],
            $updateQuery,
            ['multi' => true]
        );

        // construct update query to add date to corresponding user's 'dates' array
        $updateQuery = ['$addToSet' => ['dates' => $date]];

        // add update query to bulk write operations
        $bulkWrite->update(
            $userFilter, // filter to select the corresponding user
            $updateQuery // update query to add the date to the user's 'dates' array
        );
    }

    // execute bulk write operations to update user documents in MongoDB database
    $manager->executeBulkWrite('planning.users', $bulkWrite);

    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}


?>

<form method="POST" action="">
   <select name="years" id="years" onchange="document.getElementById('selectedYear').value=this.value; this.form.submit();">
     <option value="2023" <?php if ($selected_year === '2023') {
         echo 'selected';
     } ?>>2023</option>
     <option value="2024" <?php if ($selected_year === '2024') {
         echo 'selected';
     } ?>>2024</option>
     <option value="2025" <?php if ($selected_year === '2025') {
         echo 'selected';
     } ?>>2025</option>
     <option value="2026" <?php if ($selected_year === '2026') {
         echo 'selected';
     } ?>>2026</option>
   </select>
   <input type="hidden" name="selectedYear" id="selectedYear" value="2023">


<table style="border: 1px solid black; text-align: center;">
    <tbody>
        <?php
        $counter = 0;
        foreach ($line as $document) {
            $timestamp = strtotime($document->start_date);
            $date = date('d/m/Y', $timestamp);
            $selected_user = '';
            foreach ($users_tab as $user) {
                if (in_array($date, $user->dates)) {
                    $selected_user = $user->_id;
                    break;
                }
            }
            ?>
            <?php if ($counter % 4 == 0): ?>
                <tr>
            <?php endif; ?>
            
            <td style="border: 1px solid black;">
                <input type="hidden" name="td_dates[]" value="<?= $date ?>"><?= $date ?>
                <select name="users[<?= $date ?>]" id="users">
                    <option value="personne" <?= $selected_user === 'personne' ? 'selected' : '' ?>>Personne</option>
                    <?php foreach ($users_tab as $user): ?>
                        <option value="<?= $user->_id ?>" <?= ($selected_user === $user->_id) ? 'selected' : '' ?>><?= @$user->nom ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
            
            <?php if ($counter % 4 == 3): ?>
                </tr>
            <?php endif; ?>
            
            <?php
            $counter++;
        }
        ?>
    </tbody>
</table>


     <input name="submit-btn" id="submit-btn" type='submit' value='valider le planning'>
  
</form>
