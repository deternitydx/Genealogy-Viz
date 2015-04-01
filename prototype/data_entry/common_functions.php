<?php
$db = null;

function logger($str, $comment) {
    global $output;
    $c = "";
    if ($comment) $c = "-- ";
    fwrite($output, $c . $str . "\n");
}

function setup_db() {
    global $db;
    $db = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_data user=nauvoo password=p7qNpqygYU");
}

function query_db($q) {
    global $db;

    if ($db == null) {
        logger("Database not initialized", true);
        return null;
    }

    $result = pg_query($db, $q);
    if (!$result) {
        logger(pg_last_error($result), true);
        return null;
    }

    // Get the last insert value (if needed)
    $res = pg_query($db, "SELECT lastval()");
    $temprow = pg_fetch_Array($res);

    // Return the new id
    return $temprow[0];

}

function close_db() {
    global $db;
    pg_close($db);
}

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
    // Logging output just in case
    fwrite($output, get_insert_statement($tableName, $arr) . "\n");

    // Insert into the database

    return 1;
}

function update($tableName, $arr, $match) {
    global $output;
    // Logging output just in case
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
