<?php

$db_to = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_data_test user=nauvoo password=p7qNpqygYU");

$csvfile = fopen("names.csv", "r");
if ($csvfile == NULL)
        die("Error reading file");
$head = fgetcsv($csvfile);
$data1 = fgetcsv($csvfile);
$lu = array("Alternate" => "alternate", "Authoritative" => "authoritative");
$id = null;
while ($data1 !== false) {
       $data = array();
       foreach ($data1 as $k => $v) 
               $data[$head[$k]] = trim($v);
       if ($data["ID"] != '') { // add man to AQ
            echo "Looking up person {$data["ID"]}\n";
            $result = pg_query($db_to, "SELECT * FROM public.\"Person\" WHERE \"BYUID\"={$data["ID"]} ORDER BY \"ID\" asc");
            if (!$result) {
                echo "An error occurred.\n";
                exit;
            }

            $row = pg_fetch_array($result);
            $id = $row["ID"];
            echo "Updating names of ID $id\n";
       }


       $values = array("PersonID"=>$id, "First"=>$data["First Name"], "Middle"=>$data["Middle Name"], "Last"=>$data["Surname/Married  Name"],
               "Prefix" =>$data["Prefix"], "Suffix"=>$data["Suffix"], "Type"=>$lu[$data["Type"]]);
       if ($lu[$data["Type"]] != 'alternate') {
               $update = get_update_statement("Name", $values, "\"PersonID\"=$id AND \"Type\"='{$lu[$data["Type"]]}'");
               $res = pg_query($db_to, $update);
               if (!$res) { die("An error occured in query"); }
       }
       if ($lu[$data["Type"]] == 'alternate' || (isset($res) && pg_num_rows($res) == 0)) {
            $insert = get_insert_statement("Name", $values);
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

function get_update_statement($tableName, $arr, $match) {
    $insert = "UPDATE public.\"$tableName\" ";
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

    $insert .= " SET ($cols) = ($vals)";

    $insert .= " WHERE $match;";

    return $insert;
}
?>
