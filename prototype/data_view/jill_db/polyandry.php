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

$result = pg_query($db, "SELECT 
	  \"Marriage\".\"WifeID\", 
	    \"Person\".\"Surname\", 
	      \"Person\".\"GivenName\", 
	        \"Person\".\"BirthDate\", 
		  \"Person\".\"DeathDate\",
		    count(*) as Husbands
		    FROM 
		      public.\"Marriage\", 
		        public.\"Person\"
			WHERE 
			  \"Marriage\".\"WifeID\" = \"Person\".\"ID\"
			  GROUP BY
			    \"Marriage\".\"WifeID\", 
			      \"Person\".\"Surname\", 
			        \"Person\".\"GivenName\", 
				  \"Person\".\"BirthDate\", 
				    \"Person\".\"DeathDate\"
				    ORDER BY Husbands desc;");

if (!$result) {
    echo "An error occurred.\n";
    exit;
}

$arr = pg_fetch_all($result);

echo "<table border='1'>";
echo "<tr><th>ID</th><th>Links</th><th>Surname</th><th>Given Name</th><th>Birth Date</th><th>Death Date</th><th>Number of Husbands</th></tr>";
$json = array();
foreach ($arr as $mar) {
	$resa = array();
	foreach ($mar as $k=>$v) {
		//array_push($resa,"\"$k\": \"$v\"");
		array_push($resa, "$v");
		if ($k == "WifeID") 
			array_push($resa, "<a href=\"http://ford.cs.virginia.edu/nauvoo/api/marriages_by_woman.php?id=$v&name={$mar['Surname']}, {$mar['GivenName']}\">husbands</a>");
	}
	
	
	array_push($json, "<tr><td>" . implode("</td><td>", $resa) . "</td></tr>");


}
	echo implode("", $json);

echo "</table>";

?>

</body>
</html>
