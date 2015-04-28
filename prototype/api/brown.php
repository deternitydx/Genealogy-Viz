<?php
header('Content-type: application/json');

$db = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_data_test user=nauvoo password=p7qNpqygYU");

$result = pg_query($db, "SELECT \"id\", \"Name\", \"BD\", \"Status\", \"context\", \"PersonID\", \"Progress\" FROM public.\"Brown\" ORDER BY \"Status\", \"Name\" ASC");
if (!$result) {
    exit;
}

$brown = pg_fetch_all($result);
echo "{ \"data\": [";
$print = array();
$progress = array("unseen" => "Unseen", "inProgress" => "In Progress", "done" => "Done");
foreach ($brown as $k=>$v) {
    array_push($print, "[ \"{$v["Name"]}\", \"{$v["BD"]}\", \"{$v["context"]}\", \"{$v["Status"]}\", \"<a href='individual.php?brown={$v["id"]}&id={$v["PersonID"]}'>Edit</a>\", \"".$progress[$v["Progress"]] ."\" ]");
    //$brown[$k]["PersonID"] = "<a href='individual.php?id={$brown[$k]["PersonID"]}>Edit</a>";
    
}
echo implode(",", $print);
echo "]}"

?>
