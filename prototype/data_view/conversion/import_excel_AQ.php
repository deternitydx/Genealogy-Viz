<?php

$db_to = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_data user=nauvoo password=p7qNpqygYU");

$csvfile = fopen("aqdata.csv", "r");
if ($csvfile == NULL)
        die("Error reading file");
$head = fgetcsv($csvfile);
$data1 = fgetcsv($csvfile);
while ($data1 !== false) {
       $data = array();
       foreach ($data1 as $k => $v) 
               $data[$head[$k]] = $v;
       //print_r($data);
       if ($data["Gender"] == "M" && $data["ID"] != '') { // add man to AQ
            $result = pg_query($db_to, "SELECT * FROM public.\"Person\" WHERE \"BYUID\"={$data["ID"]} ORDER BY \"ID\" asc");
            if (!$result) {
                echo "An error occurred.\n";
                exit;
            }

            $row = pg_fetch_array($result);
            //print_r($row);
            $id = $row["ID"];
            echo "Inserting ID $id into AQ\n";

            $insert = get_insert_statement("ChurchOrgMembership", array("PersonID"=>$id, "ChurchOrgID"=>1));
            $res = pg_query($db_to, $insert);
       }
       $data1 = fgetcsv($csvfile);
}

fclose($csvfile);

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
?>
