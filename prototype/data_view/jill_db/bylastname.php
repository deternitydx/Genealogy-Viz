<html>
<style>

td {
   padding: 4px;
   margin: 0px;
   border: 1px solid black;
}

table {
   border: 1px solid black;
   border-spacing: 0px;
}

tr {
   border: 1px solid black;
}

th {
   color: #ffffff;
   background: #444444;
}

</style>
<body>
<?php

//header('Content-type: application/json');

$query = "Smith";
if (isset($_GET["q"]))
	$query = $_GET["q"];
	

$db = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_new user=nauvoo password=p7qNpqygYU");

$result = pg_query($db, "SELECT * FROM public.\"Person\" WHERE \"Surname\"='$query' ORDER BY \"Surname\", \"GivenName\" asc");
if (!$result) {
    echo "An error occurred.\n";
    exit;
}

$arr = pg_fetch_all($result);
echo "Results: " . count($arr);
echo "<table>";
$json = array();
$first = true;
foreach ($arr as $mar) {
	$resa = array();
	if ($first) $headings = array();
	foreach ($mar as $k=>$v) {
		//array_push($resa,"\"$k\": \"$v\"");
		array_push($resa, "$v");
		if ($first) array_push($headings, "$k");
	}
	
	
	if ($first) 
		array_push($json, "<tr><th>" . implode("</th><th>", $headings) . "</th></tr>");
	array_push($json, "<tr><td>" . implode("</td><td>", $resa) . "</td></tr>");
	$first = false;


}
	echo implode("", $json);

echo "</table>";

?>
</body>
</html>
