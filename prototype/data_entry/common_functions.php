<?php
function get_insert_statement($tableName, $arr) {
    $insert = "INSERT INTO public.\"$tableName\" ";
    $cols = "";
    $vals = "";
    foreach ($arr as $k => $v) {
        $cols .= "\"$k\",";
        if ($v == "") $v = "NULL";
        if (is_numeric($v) || $v == "NULL")
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
        if (is_numeric($v) || $v == "NULL")
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

function insert($tableName, $arr) {
    global $output;
    fwrite($output, get_insert_statement($tableName, $arr) . "\n");

    // must return the last id after inserting
    return 1;
}

function update($tableName, $arr, $match) {
    global $output;
    fwrite($output, get_update_statement($tableName, $arr, $match) . "\n");

    // must return true/false if the update succeeded
    return false;
}


function combine_date($year, $month, $day) {
    $date = "";
    if ($year != "YYYY" && $year != "") {
        $date .= $year;
        if ($month != "MM" && $month != "") {
            $date .= "-" . $month;
            if ($day != "DD" && $day != "") {
                $date .= "-" . $day;
            }
        }
    }
    
    return $date;
}
?>
