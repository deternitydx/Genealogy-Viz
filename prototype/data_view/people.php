<html>
<head>
<title>People</title>
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="/nauvoo/css/style.css"/>
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.2/css/jquery.dataTables.css"/>
  
<!-- jQuery -->
<script type="text/javascript" charset="utf8" src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
  
<!-- DataTables -->
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.2/js/jquery.dataTables.js"></script>

</head>
<body>
<script>
$(document).ready( function () {
    $('#datatable').DataTable( {paging: true, ajax: "/nauvoo/api/people.php", deferRender: true});
} );
</script>
<h1>People</h1>
<?php
echo "<table id='datatable' class='display'>";
echo "<thead><tr><th>ID</th><th>First</th><th>Middle</th><th>Last</th><th>Birth Date</th><th>Death Date</th><th>Gender</th><th>Birth Place ID</th></tr></thead>";
echo "</table>";
/**
$db = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_data user=nauvoo password=p7qNpqygYU");

$result = pg_query($db, "SELECT p.\"ID\",n.\"First\",n.\"Middle\",n.\"Last\",p.\"BirthDate\",p.\"DeathDate\",
       p.\"Gender\", p.\"BirthPlaceID\" FROM public.\"Person\" p, public.\"Name\" n WHERE p.\"ID\"=n.\"PersonID\" AND n.\"Type\"='authoritative'ORDER BY n.\"Last\", n.\"First\",n.\"Middle\" asc");
if (!$result) {
    echo "An error occurred.\n";
    exit;
}

$arr = pg_fetch_all($result);
echo "<table id='datatable' class='display'>";
$json = array();
$first = true;
foreach ($arr as $mar) {
	$resa = array();
	if ($first) $headings = array();
	foreach ($mar as $k=>$v) {
            //array_push($resa,"\"$k\": \"$v\"");
        if ($v == "") $v = "&nbsp;";
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
 */
?>
</body>
</html>
