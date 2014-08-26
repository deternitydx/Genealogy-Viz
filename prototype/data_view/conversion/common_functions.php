<?php
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
