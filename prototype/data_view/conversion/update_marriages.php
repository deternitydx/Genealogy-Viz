<?php

$db_from = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_data user=nauvoo password=p7qNpqygYU");
$db_to = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_data user=nauvoo password=p7qNpqygYU");


echo "Grabbing all the Marriage records.\n";

$result = pg_query($db_from, "SELECT * FROM public.\"Marriage\" ORDER BY \"ID\" asc");
if (!$result) {
    echo "An error occurred.\n";
    exit;
}

// Loop over each marriage in the list
while ($row = pg_fetch_array($result)) {

        $id = $row["ID"];

        if ($row["HusbandID"] != "NULL") {
                $insert = get_insert_statement("PersonMarriage", array("PersonID"=>$row["HusbandID"],
                        "Role"=>"Husband", "MarriageID"=>$id));
                $res = pg_query($db_to, $insert);
        }
        if ($row["WifeID"] != "NULL") {
                $insert = get_insert_statement("PersonMarriage", array("PersonID"=>$row["WifeID"],
                        "Role"=>"Wife", "MarriageID"=>$id));
                $res = pg_query($db_to, $insert);
        }
    echo "Importing record for {$row["ID"]} :: $id.\n";
}


function get_insert_statement($tableName, $arr) {
    $insert = "INSERT INTO public.\"$tableName\" ";
    $cols = "";
    $vals = "";
    foreach ($arr as $k => $v) {
            $cols .= "\"$k\",";
            if ($v == "") $v = "NULL";
            if ($k == "ID" || $k == "BYUChildOf" || $v == "NULL")
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
