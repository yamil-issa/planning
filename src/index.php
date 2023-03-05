<?php
ob_start();
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

    $cursor = $manager->executeQuery('planning.users', $read);
    $line = $manager->executeQuery('planning.dates', $query);
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
    $users = $_POST['users'];

    // create BulkWrite
    $bulkWrite = new MongoDB\Driver\BulkWrite();

    foreach ($users as $date => $userId) {
        if ($userId === 'personne') {
            continue;
        }
        // retrieve user document to check if the date is already assigned
        $userFilter = ['_id' => new MongoDB\BSON\ObjectId($userId)];
        $userQuery = new MongoDB\Driver\Query($userFilter);
        $userDocument = current($manager->executeQuery('planning.users', $userQuery)->toArray());

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

        $updateQuery = ['$addToSet' => ['dates' => $date]];

        $bulkWrite->update(
            $userFilter,
            $updateQuery
        );
    }


    $manager->executeBulkWrite('planning.users', $bulkWrite);

    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <title>planning</title>
</head>
<body>
    <h1>
        Planning
    </h1>
    <script src="../js/app.js"></script>




<form method="POST" action="">
   <select class="form-select" name="years" id="years_select" onchange="document.getElementById('selectedYear').value=this.value; this.form.submit();">
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


<table>
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
            
            <td>
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


     <input name="submit-btn" class="btn btn-primary" id="submit-btn" type='submit' value='valider le planning'>
  
</form>
<?php include('stat.php')?>
</body>
</html>
