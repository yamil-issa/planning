<?php

require '../model/connection.php';


//define the bulk 
$bulk = new MongoDB\Driver\BulkWrite;


// Define start and end dates
$start_year = 2023;
$end_year = 2026;
$start_date = new DateTime();
$start_date->setISODate($start_year, 1);
$end_date = new DateTime();
$end_date->setISODate($end_year, 53);

// Loop
$current_date = $start_date;
while ($current_date <= $end_date) {
    $year = $current_date->format('Y');
    $week = $current_date->format('W');
    $document = [
        'year' => $year,
        'week' => $week,
        'start_date' => $current_date->format('Y-m-d'),
        'end_date' => $current_date->modify('+6 days')->format('Y-m-d'),
    ];
    $bulk->insert($document);
    $current_date->modify('+1 day');
}

    $manager->executeBulkWrite('planning.dates', $bulk);
    
echo 'Documents inserted successfully.';
