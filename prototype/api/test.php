<?php

//header('Content-type: application/json');

$id = 14;
if (isset($_GET["id"]))
	$id = $_GET["id"];
	

$db = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_data user=nauvoo password=p7qNpqygYU");

$result = pg_query($db, "SELECT * FROM public.\"Marriage\" ORDER BY \"HusbandID\" asc");
if (!$result) {
    echo "An error occurred.\n";
    exit;
}

$arr = pg_fetch_all($result);

echo "<table border='1'>";
echo "<tr><td>ID</td><td>Husband</td><td>Wife</td><td>Date</td><td>Date</td><td>Place</td><td>Comments</td></tr>";
$json = array();
foreach ($arr as $mar) {
	$resa = array();
	foreach ($mar as $k=>$v) {
		//array_push($resa,"\"$k\": \"$v\"");
		array_push($resa, "$v");
	}
	
	
	array_push($json, "<tr><td>" . implode("</td><td>", $resa) . "</td></tr>");


}
	echo implode("", $json);

echo "</table>";

?>
