<?php
    $id = null;
    // Get the person ID
    if (isset($_GET["id"]) && is_numeric($_GET["id"]))
        $id = $_GET["id"];
    else
        die("Please provide a numeric id");
    
    header('Content-type: application/json');
    
    $db = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_data user=nauvoo password=p7qNpqygYU");
    
    // Array to hold all information about the person
    $person = array();
    
    $result = pg_query($db, "SELECT * FROM public.\"Person\" p
    WHERE p.\"ID\"=$id");
    if (!$result) {
        exit;
    }
    foreach($result as $res) {
        $person["information"] = $res;
    }
    
    $result = pg_query($db, "SELECT * FROM public.\"Name\" n WHERE n.\"ID\"=$id");
    if (!$result) {
        exit;
    }
    foreach($result as $res) {
        $person["names"] = $res;
    }
    
    // Return the person array as json to be used by the editor:
    echo json_encode($person);
?>