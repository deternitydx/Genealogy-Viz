<?php
    header('Content-type: application/json');
    
    $db = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_data_test user=nauvoo password=p7qNpqygYU");

    $result = pg_query($db, "SELECT * FROM public.\"Place\"");
    if (!$result) {
        exit;
    }
    $places = pg_fetch_all($result);

    echo json_encode($places);
?>
