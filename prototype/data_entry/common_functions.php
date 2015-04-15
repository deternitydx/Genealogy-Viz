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
    $db = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_data_test user=nauvoo password=p7qNpqygYU");
}

function query_db($q) {
    global $db;

    if ($db == null) {
        logger("Database not initialized", true);
        return false;
    }

    $result = pg_query($db, $q);
    if (!$result) {
        logger(pg_result_error($result), true);
        return false;
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
        $cols .= pg_escape_identifier($k) . ",";
        if ($v == "") $v = "NULL";
        if (is_numeric($v) || $v == "NULL")
            $vals .= "$v,";
        else
            $vals .= pg_escape_literal($v) . ",";
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
        $cols .= pg_escape_identifier($k) .",";
        if ($v == "") $v = "NULL";
        if (is_numeric($v) || $v == "NULL")
            $vals .= "$v,";
        else
            $vals .= pg_escape_literal($v) . ",";
    }
    $cols = substr($cols, 0, -1);
    $vals = substr($vals, 0, -1);

    $insert .= " SET ($cols) = ($vals)";

    $insert .= " WHERE $match;";

    return $insert;
}

function insert($tableName, $arr) {
    global $output;

    $insert = get_insert_statement($tableName, $arr);
    // Logging output just in case
    logger($insert, false);
    return query_db($insert);
}

function update($tableName, $arr, $match) {
    global $output;

    $update = get_update_statement($tableName, $arr, $match);
    
    // Logging output just in case
    logger($update, false);
    return query_db($update) === false ? false : true;
}


function combine_date($year, $month, $day) {
    $date = "";
    if ($year != "YYYY" && $year != "") {
        $date .= $year;
        if ($month != "MM" && $month != "") {
            if (intval($month) < 10)
                $date .= "-0" . intval($month);
            else
                $date .= "-" . intval($month);
            if ($day != "DD" && $day != "") {
                if (intval($day) < 10)
                    $date .= "-0" . intval($day);
                else
                    $date .= "-" . intval($day);
            }
        }
    }
    
    return $date;
}
?>
