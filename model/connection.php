<?php

//connexion à
try {
 $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017/");
 
}
catch ( MongoDB\Driver\Exception\Exception $e )
{
 echo "Probleme! : ".$e->getMessage();
 exit();
}


?>

