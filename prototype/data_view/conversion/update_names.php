<?php

$db_from = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_new user=nauvoo password=p7qNpqygYU");
$db_to = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_data user=nauvoo password=p7qNpqygYU");


echo "Grabbing all the Person records.\n";

$result = pg_query($db_from, "SELECT * FROM public.\"Person\" ORDER BY \"ID\" asc");
if (!$result) {
    echo "An error occurred.\n";
    exit;
}

// Loop over each person in the list
while ($row = pg_fetch_array($result)) {
        $link = pg_query($db_to, "SELECT \"ID\" FROM public.\"Person\" WHERE \"BYUID\"={$row['ID']}");
        if (!$link) { die ("An error occured."); }

        $tmp = pg_fetch_array($link);
        $id = $tmp["ID"];


        // insert the person's name as an authoritative name
        $arr = array ("PersonID" => $id,
            "First" => pg_escape_string($row['GivenName']),
            "Last" => pg_escape_string($row['Surname']),
            "Type" => "authoritative");
        $insert = get_insert_statement("Name", $arr);
        $res = pg_query($db_to, $insert);
        //echo $insert . "\n";

    echo "Importing record for {$row["ID"]} :: $id.\n";
}

print_r($places);


function get_insert_statement($tableName, $arr) {
    $insert = "INSERT INTO public.\"$tableName\" ";
    $cols = "";
    $vals = "";
    foreach ($arr as $k => $v) {
            $cols .= "\"$k\",";
            if ($v == "") $v = "NULL";
            if ($k == "BYUID" || $k == "BYUChildOf" || $v == "NULL")
                $vals .= "$v,";
            else
                $vals .= "'$v',";
    }
    $cols = substr($cols, 0, -1);
    $vals = substr($vals, 0, -1);

    $insert .= "($cols) VALUES ($vals);";

    return $insert;
}

function get_place($place) {
    global $places;
    global $db_to;
    if (!isset($place) || empty($place) || $place == null || $place == "NULL" || $place == "" || $place == "null")
            return "NULL";

    $arr = array("OfficialName" => pg_escape_string($place));

    $insert = get_insert_statement("Place", $arr);
    $res = pg_query($db_to, $insert);

    if ($res) {
        $res = pg_query($db_to, "SELECT lastval()");
        $temprow = pg_fetch_Array($res);
        $id = $temprow[0];
        $places[$place] = $id;
        return $id;
    }

    return "NULL";
}

function load_places($db) {
    $places = array();

    $result = pg_query($db, "SELECT * FROM public.\"Place\" ORDER BY \"ID\" asc");
    if (!$result) {
        echo "An error occurred.\n";
        exit;
    }

    // Loop over each place in the list
    while ($row = pg_fetch_array($result)) {
        $places[$row["OfficialName"]] = $row["ID"];
    }

    return $places;
}
?>
