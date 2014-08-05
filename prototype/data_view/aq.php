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
	

$db = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_data user=nauvoo password=p7qNpqygYU");

$result = pg_query($db, "SELECT DISTINCT ON (n.\"Last\",p.\"ID\") p.\"BYUID\",p.\"ID\",n.\"First\", n.\"Middle\", n.\"Last\",p.\"BirthDate\",p.\"DeathDate\", p.\"Gender\" FROM \"Person\" p LEFT JOIN \"Name\" n ON (p.\"ID\" = n.\"PersonID\" AND n.\"Type\" = 'authoritative') LEFT JOIN \"ChurchOrgMembership\" m ON (m.\"PersonID\" = p.\"ID\") LEFT JOIN \"ChurchOrganization\" c ON (m.\"ChurchOrgID\" = c.\"ID\") WHERE c.\"Name\" = 'Annointed Quorum' ORDER BY n.\"Last\", p.\"ID\" ASC");
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
    $addl = "";
    if ($mar["Gender"] == "Female")
        $addl = "&wife=1";

	foreach ($mar as $k=>$v) {
            //array_push($resa,"\"$k\": \"$v\"");
        if ($k == "ID")
                array_push($resa, "<a href=\"../chord.html?id=$v$addl\">$v</a>");
        else
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
