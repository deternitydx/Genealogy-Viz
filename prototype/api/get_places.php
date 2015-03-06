<?php
    header('Content-type: application/json');

    if (!isset($_GET['q'])) {
        echo "{ 'error': 'no search term given'}";
        die();
    }

    $query = $_GET['q'];

    $db = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_data_test user=nauvoo password=p7qNpqygYU");

    $result = pg_query($db, "SELECT \"ID\" as \"id\", \"OfficialName\" as \"text\"  FROM public.\"Place\" WHERE lower(\"OfficialName\") ilike '%$query%' ORDER BY \"OfficialName\" ASC");
    if (!$result) {
        exit;
    }
    $places = pg_fetch_all($result);

    echo json_encode($places);
?>
