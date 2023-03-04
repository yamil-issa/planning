<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <h2>Statistiques</h2>
    <ul>
        <?php foreach ($users_tab as $user):
            $year = $selected_year;

            $pipeline = array(
                array('$match' => array('_id' => $user->_id)),
                array('$unwind' => '$dates'),
                array('$match' => array('dates' => array('$regex' => "^.*\/$year$"))),
                array('$group' => array('_id' => '$_id', 'count' => array('$sum' => 1)))
            );

            $command = new MongoDB\Driver\Command(array('aggregate' => 'users', 'pipeline' => $pipeline, 'cursor' => new stdClass));

            $cursor = $manager->executeCommand('planning', $command);

            @$result = $cursor->toArray()[0];
        ?>
            <li><?=@$user->nom?>  : <?=isset($result->count) ? $result->count : 0?> </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
