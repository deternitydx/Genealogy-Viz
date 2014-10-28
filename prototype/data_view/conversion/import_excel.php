<?php

//$db_to = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_data_test user=nauvoo password=p7qNpqygYU");

$csvfile = fopen("aqdata.csv", "r");
if ($csvfile == NULL)
        die("Error reading file");
$head = fgetcsv($csvfile);
$data1 = fgetcsv($csvfile);
while ($data1 !== false) {
       $data = array();
       foreach ($data1 as $k => $v) 
               $data[$head[$k]] = $v;
       print_r($data) ;
       $data1 = fgetcsv($csvfile);
}

fclose($csvfile);
?>
