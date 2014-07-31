<?php

//header('Content-type: application/json');

$id = 14;
$ln = "";
if (isset($_GET["id"]))
	$id = $_GET["id"];

if (isset($_GET["q"])) {
    $ln = $_GET["q"];
}

$db = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_new user=nauvoo password=p7qNpqygYU");

$addTo = "";
if ($ln != "") {
        $addTo .= " AND h.\"Surname\" = '$ln' ";
}
$result = pg_query($db, "SELECT m.\"ID\", m.\"HusbandID\", h.\"Surname\" as hs, h.\"GivenName\" as hg, m.\"WifeID\", w.\"Surname\" as ws, w.\"GivenName\" as wg, m.\"MarriageDate\", m.\"DivorceDate\", m.\"MarriagePlace\", m.\"Comments\" FROM public.\"Marriage\" m, public.\"Person\" h, public.\"Person\" w  WHERE h.\"ID\" = m.\"HusbandID\" AND w.\"ID\" = m.\"WifeID\" $addTo ORDER BY hs, hg, ws, wg, \"ID\" asc");
if (!$result) {
    echo "An error occurred.\n";
    exit;
}

$arr = pg_fetch_all($result);

echo "<table border='1'>";
echo "<tr><td>ID</td><td>Husband ID</td><td>Surname</td><td>GivenName</td><td>Wife ID</td><td>Surname</td><td>GivenName</td><td>MarriageDate</td><td>DivorceDate</td><td>Place</td><td>Comments</td></tr>";
$json = array();
foreach ($arr as $mar) {
	$resa = array();
	foreach ($mar as $k=>$v) {
		//array_push($resa,"\"$k\": \"$v\"");
		array_push($resa, "$v");
		if ($k == "MarriageDateText")
			if ($v == "")
				array_push($resa, "");
			else {
				if (strlen($v) == 4) // likely a year
					$v = $v . "-01-01";
				array_push($resa, date_format(date_create($v), "Y-m-d"));
			}	
	}
	
	array_push($json, "<tr><td>" . implode("</td><td>", $resa) . "</td></tr>");


}
	echo implode("", $json);

echo "</table>";

?>
