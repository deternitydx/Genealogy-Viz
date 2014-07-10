<?php

$db_from = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_new user=nauvoo password=p7qNpqygYU");
$db_to = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_data user=nauvoo password=p7qNpqygYU");


echo "Grabbing all the Person records.\n";

$result = pg_query($db_from, "SELECT * FROM public.\"Person\" ORDER BY \"ID\" asc");
if (!$result) {
    echo "An error occurred.\n";
    exit;
}

$gender = array( "M" => "Male", "F" => "Female");

$i = 0;

$places = array();

// grab all the places
$places = load_places($db_to);

print_r($places);
// Loop over each person in the list
while ($row = pg_fetch_array($result)) {
    // do stuff with row
        if ($i++ == 10) return;
        $birthplace = "";
        $deathplace = "";
        $burialplace = "";

        if ($row["ChildOfMarriageID"] == "") 
                $row["ChildOfMarriageID"] = "NULL";

        if (array_key_exists($row["BirthPlace"], $places))
                $birthplace = $places[$row["BirthPlace"]];
        else
                $birthplace = get_place($row["BirthPlace"]);

        if (array_key_exists($row["DeathPlace"], $places))
                $deathplace = $places[$row["DeathPlace"]];
        else
                $deathplace = get_place($row["DeathPlace"]);

        if (array_key_exists($row["BurialPlace"], $places))
                $burialplace = $places[$row["BurialPlace"]];
        else
                $burialplace = get_place($row["BurialPlace"]);

        $arr = array ("BYUID" => $row['ID'],
                        "BirthDate" => $row['BirthDate'],
                        "BirthPlaceID" => $birthplace,
                        "DeathDate" => $row['DeathDate'],
                        "DeathPlaceID" => $deathplace,
                        "BurialPlaceID" => $burialplace,
                        "Gender" => $gender[$row["Gender"]],
                        "BYUChildOf" => $row['ChildOfMarriageID']);
    $id = "";

    $insert = get_insert_statement("Person", $arr);
    $res = pg_query($db_to, $insert);
    if ($res) {
        $res = pg_query($db_to, "SELECT lastval()");
        $temprow = pg_fetch_Array($res);

        // grab this person's ID in the new database
        $id = $temprow[0];

        // insert the person's name as an authoritative name
        $arr = array ("PersonID" => $id,
            "First" => $row['GivenName'],
            "Last" => $row['Surname'],
            "Type" => "authoritative");
        $insert = get_insert_statement("Name", $arr);
        $res = pg_query($db_to, $insert);

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

    echo "inserting for place: $place\n";
    $arr = array("OfficialName" => $place);

    $insert = get_insert_statement("Place", $arr);
    $res = pg_query($db_to, $insert);

    if ($res) {
        $res = pg_query($db_to, "SELECT lastval()");
        $temprow = pg_fetch_Array($res);
        $id = $temprow[0];
        $places[$place] = $id;
        echo "inserted new place at: $id";
        print_r($places);
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
