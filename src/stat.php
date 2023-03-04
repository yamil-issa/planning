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
        
        <?php foreach ($users_tab as $user): ?>
            <li><?= @$user->nom ?> :</li>
        <?php endforeach; ?>
    </ul>
</body>
</html>

<?php

?>
