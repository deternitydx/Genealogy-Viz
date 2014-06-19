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

// Loop over each person in the list
while ($row = pg_fetch_array($result)) {
    // do stuff with row
    if ($i++ == 10) return;
        $arr = array ("BYUID" => $row['ID'],
                        "BirthDate" => $row['BirthDate'],
                        "BirthPlace" => $row["BirthPlace"],
                        "DeathDate" => $row['DeathDate'],
                        "DeathPlace" => $row["DeathPlace"],
                        "BurialPlace" => $row["BurialPlace"],
                        "Gender" => $gender[$row["Gender"]]);
    if ($row["LDS"] == 1)
            $arr["LDSMember"] = "true";
    else
            $arr["LDSMember"] = "false";
    $insert = get_insert_statement("Person", $arr);
    $res = pg_query($db_to, $insert);
    if ($res) {
        $res = pg_query($db_to, "SELECT lastval()");
        $temprow = pg_fetch_Array($res);
        $id = $temprow[0];

        $arr = array ("PersonID" => $id,
            "First" => $row['GivenName'],
            "Last" => $row['Surname'],
            "Type" => "authoritative");
        $insert = get_insert_statement("Name", $arr);
        $res = pg_query($db_to, $insert);
        return;

    }
    echo "==================================================\nUpdating record for {$row["ID"]}.\n";
    //echo $insert . "\n";
    $key = 1; // insert statement goes here, hopefully returning the id


    

}

function get_insert_statement($tableName, $arr) {
    $insert = "INSERT INTO public.\"$tableName\" ";
    $cols = "";
    $vals = "";
    foreach ($arr as $k => $v) {
            $cols .= "\"$k\",";
            if ($k == "BYUID")
                $vals .= "$v,";
            else
                $vals .= "'$v',";
    }
    $cols = substr($cols, 0, -1);
    $vals = substr($vals, 0, -1);

    $insert .= "($cols) VALUES ($vals);";

    return $insert;
}
?>
