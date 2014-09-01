<?php

$db_from = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_new user=nauvoo password=p7qNpqygYU");
$db_to = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_data user=nauvoo password=p7qNpqygYU");


echo "Grabbing all the Marriage records.\n";

$result = pg_query($db_from, "SELECT * FROM public.\"Marriage\" ORDER BY \"ID\" asc");
if (!$result) {
    echo "An error occurred.\n";
    exit;
}

$gender = array( "M" => "Male", "F" => "Female", "[F]" => "Female", "[M]" => "Male", "[U]" => "NULL", "NULL" => "NULL");

$places = array();

// grab all the places
$places = load_places($db_to);

print_r($places);
// Loop over each person in the list
while ($row = pg_fetch_array($result)) {
        $marriageplace = "";

        if (array_key_exists($row["MarriagePlace"], $places))
                $marriageplace = $places[$row["MarriagePlace"]];
        else
                $marriageplace = get_place($row["MarriagePlace"]);


        $arr = array ("BYUID" => $row['ID'],
                        "PlaceID" => $marriageplace,
                        "MarriageDate" => $row['MarriageDate'],
                        "DivorceDate" => $row['DivorceDate']);
    $id = "";

    $insert = get_insert_statement("Marriage", $arr);
    $res = pg_query($db_to, $insert);
    if ($res) {
        $res = pg_query($db_to, "SELECT lastval()");
        $temprow = pg_fetch_Array($res);

        // grab this marriage's ID in the new database
        $id = $temprow[0];

        // insert husband and wife into personmarriage
        if (isset($row["HusbandID"]) && $row["HusbandID"] != null) {
                $arr = array( "PersonID" => get_new_id($row["HusbandID"]), "MarriageID" => $id, "Role" => "Husband");
                $ins = get_insert_statement("PersonMarriage", $arr);
                $res = pg_query($db_to, $ins);
                if (!$res) {
                        echo "Error in query: " . $ins;
                        print_r($row); die();
                }
        }
        if (isset($row["WifeID"]) && $row["WifeID"] != null) {
                $arr = array( "PersonID" => get_new_id($row["WifeID"]), "MarriageID" => $id, "Role" => "Wife");
                $ins = get_insert_statement("PersonMarriage", $arr);
                $res = pg_query($db_to, $ins);
                if (!$res) {
                        echo "Error in query: " . $ins;
                        print_r($row); die();
                }
        }

        // update the children's links in the person table
        $res = pg_query($db_to, "UPDATE public.\"Person\" set \"BiologicalChildOfMarriage\"=$id where \"BYUChildOf\"={$row['ID']}");

    }
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

function get_new_id($byuid) {
    global $db_to;

    if (!isset($byuid) || empty($byuid) || $byuid == ""){
            echo "Empty person ID";
            return "NULL";
    }

    $result = pg_query($db_to, "SELECT * FROM public.\"Person\" WHERE \"BYUID\"=$byuid");
    if (!$result) {
        echo "An error occurred.\n";
        exit;
    }

    // Do the test in a while to be safe
    while ($row = pg_fetch_array($result)) {
        return $row["ID"];
    }

    return "NULL";
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
