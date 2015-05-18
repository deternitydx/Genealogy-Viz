<html>
<head>
<title>Sealings Pre-Dec1845</title>
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="/nauvoo/css/style.css"/>
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.2/css/jquery.dataTables.css"/>
  
<!-- jQuery -->
<script type="text/javascript" charset="utf8" src="../js/jquery-2.1.1.js"></script>
  
<!-- DataTables -->
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.2/js/jquery.dataTables.js"></script>

</head>
<body>
<script>
$(document).ready( function () {
    $('#datatable').DataTable( {paging: false});
} );
</script>

<h1>Sealings Before December 1845</h1>
<?php

//header('Content-type: application/json');

$query = "Smith";
if (isset($_GET["q"]))
	$query = $_GET["q"];
	

include("../database.php");
$db = pg_connect($db_conn_string);

$result = pg_query($db, "SELECT DISTINCT h.\"First\" as \"HusbandFirst\", h.\"Last\" as \"HusbandLast\", w.\"First\" as \"WifeFirst\", w.\"Last\" as \"WifeLast\",m.\"Type\",m.\"MarriageDate\" as \"Date\", p.\"OfficialName\" as \"Place\", m.\"PrivateNotes\" as \"Notes\" from \"Marriage\" m LEFT JOIN \"Place\" p ON p.\"ID\"=m.\"PlaceID\",\"PersonMarriage\" pm, \"PersonMarriage\" pmw, \"Name\" h, \"Name\" w WHERE  m.\"ID\" = pm.\"MarriageID\" AND m.\"ID\" = pmw.\"MarriageID\" AND h.\"PersonID\"=pm.\"PersonID\" AND pm.\"Role\"='Husband' AND h.\"Type\" = 'authoritative' AND w.\"PersonID\"=pmw.\"PersonID\" AND pmw.\"Role\"='Wife' AND w.\"Type\" = 'authoritative' AND \"MarriageDate\" < '1845-12-10' AND m.\"Type\" IN ('eternity', 'time') ORDER BY m.\"MarriageDate\"");
if (!$result) {
    echo "An error occurred.\n";
    exit;
}

$arr = pg_fetch_all($result);
echo "Results: " . count($arr);
echo "<table id='datatable' class='display'>";
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
		array_push($json, "<thead><tr><th>" . implode("</th><th>", $headings) . "</th></tr></thead><tbody>");
	array_push($json, "<tr><td>" . implode("</td><td>", $resa) . "</td></tr>");
	$first = false;


}
	echo implode("", $json);

echo "</tbody></table>";

?>
</body>
</html>
