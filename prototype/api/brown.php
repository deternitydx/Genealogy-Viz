<?php
header('Content-type: application/json');

$db = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_data_test user=nauvoo password=p7qNpqygYU");

$result = pg_query($db, "SELECT \"id\", \"Name\", \"BD\", \"Status\", \"PersonID\" FROM public.\"Brown\" ORDER BY \"Status\", \"Name\" ASC");
if (!$result) {
    exit;
}

$brown = pg_fetch_all($result);
echo "{ \"data\": [";
$print = array();
foreach ($brown as $k=>$v) {
    array_push($print, "[ \"{$v["Name"]}\", \"{$v["BD"]}\", \"{$v["Status"]}\", \"<a href='individual.php?brown={$v["id"]}&id={$v["PersonID"]}'>Edit</a>\" ]");
    //$brown[$k]["PersonID"] = "<a href='individual.php?id={$brown[$k]["PersonID"]}>Edit</a>";
    
}
echo implode(",", $print);
echo "]}"

?>
