<?php

$db_to = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_data_test user=nauvoo password=p7qNpqygYU");

$result = pg_query($db_to, "select * from (select \"PersonID\", \"First\", \"Middle\", \"Last\", \"Type\", count(*) as Count from \"Name\" group by \"First\",\"Middle\",\"Last\",\"Type\",\"PersonID\" order by Count desc) c where Count > 1;");
if (!$result) {
    echo "An error occurred.\n";
    exit;
}

$list = pg_fetch_all($result);

print_r($list);

foreach ($list as $n) {
    $result = pg_query($db_to, "select * from \"Name\" where \"PersonID\" = {$n["PersonID"]};");
    if (!$result) {
        echo "An error occurred.\n";
        exit;
    }
    $names = pg_fetch_all($result);
    print_r($names);

    foreach ($names as $k => $name) {
        if ($k != 0) {
            // remove the additional name
            $q = "delete from \"Name\" where \"ID\" = {$name["ID"]};";
            echo $q . "\n";
            pg_query($db_to, $q);
        }
    }

}


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
