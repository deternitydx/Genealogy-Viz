<?php

$db = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_new user=nauvoo password=p7qNpqygYU");


echo "Grabbing all the Person records.\n";

$result = pg_query($db, "SELECT * FROM public.\"Person\" ORDER BY \"ID\" asc");
if (!$result) {
    echo "An error occurred.\n";
    exit;
}

$arr = pg_fetch_all($result);
echo "Results: " . count($arr);
echo "<table border='1'>";
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
