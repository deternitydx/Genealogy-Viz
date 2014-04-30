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

$id = 14;
if (isset($_GET["id"]))
	$id = $_GET["id"];
	

$db = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_new user=nauvoo password=p7qNpqygYU");

$result = pg_query($db, "SELECT CO.\"Name\", M.\"StartDate\", M.\"EndDate\", P.\"Surname\", P.\"GivenName\" FROM public.\"Membership\" as M, public.\"ChurchOrgs\" as CO, public.\"Person\" as P WHERE M.\"PersonID\" = P.\"ID\" AND M.\"ChurchOrgID\" = CO.\"ID\" ORDER BY CO.\"ID\", P.\"Surname\" asc");
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
