<?php
    header('Content-type: application/json');

    if (!isset($_GET['q'])) {
        echo "{ 'error': 'no search term given'}";
        die();
    }

    $q = $_GET['q'];

    $db = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_data_test user=nauvoo password=p7qNpqygYU");

    $query = "
        SELECT DISTINCT p.*, n.\"First\", n.\"Last\", n.\"Type\"

        FROM public.\"Name\" n

        LEFT JOIN public.\"Person\" p ON p.\"ID\" = n.\"PersonID\" 
        
        WHERE 
        n.\"First\" || ' ' || n.\"Last\" ilike '%$q%'

        ORDER BY n.\"Last\", n.\"First\" ASC";
    $result = pg_query($db, $query);
    if (!$result) {
        exit;
    }
    $results = pg_fetch_all($result);

    $people = array();

    foreach($results as $res) {
        array_push($people, array("id"=>$res["ID"], "text"=> $res["Last"] . ", " . $res["First"] . " (" . $res["BirthDate"] . " -- " . $res["DeathDate"] . ")"));
    }
    echo json_encode($people);
?>
