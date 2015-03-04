<?php
header('Content-type: application/json');

if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
    $id = $_GET["id"];

    $db = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_data_test user=nauvoo password=p7qNpqygYU");

    $result = pg_query($db, "SELECT * FROM public.\"Brown\" WHERE \"id\"=$id");
    if (!$result) {
        echo "{ \"error\": \"Person could not be found\"}";
        exit;
    }

    echo json_encode(pg_fetch_all($result));
} else 
    echo "{ \"error\": \"ID is not set or is not numeric.\"}";
?>
