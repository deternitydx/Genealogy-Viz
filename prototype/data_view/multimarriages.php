<?php

//header('Content-type: application/json');

$id = 14;
if (isset($_GET["id"]))
	$id = $_GET["id"];
	

$db = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_new user=nauvoo password=p7qNpqygYU");

$result = pg_query($db, "SELECT 
	  \"Marriage\".\"HusbandID\", 
	    \"Person\".\"Surname\", 
	      \"Person\".\"GivenName\", 
	        \"Person\".\"BirthDateSearchable\", 
		  \"Person\".\"DeathDateSearchable\",
		    count(*) as Wives
		    FROM 
		      public.\"Marriage\", 
		        public.\"Person\"
			WHERE 
			  \"Marriage\".\"HusbandID\" = \"Person\".\"ID\"
			  GROUP BY
			    \"Marriage\".\"HusbandID\", 
			      \"Person\".\"Surname\", 
			        \"Person\".\"GivenName\", 
				  \"Person\".\"BirthDateSearchable\", 
				    \"Person\".\"DeathDateSearchable\"
				    ORDER BY Wives desc;");

if (!$result) {
    echo "An error occurred.\n";
    exit;
}

$arr = pg_fetch_all($result);

echo "<table border='1'>";
echo "<tr><td>ID</td><td>Surname</td><td>Given Name</td><td>Birth Date</td><td>Death Date</td><td>Number of Wives</td></tr>";
$json = array();
foreach ($arr as $mar) {
	$resa = array();
	foreach ($mar as $k=>$v) {
		//array_push($resa,"\"$k\": \"$v\"");
		if ($k == "HusbandID")
			array_push($resa, "<a href=\"http://ford.cs.virginia.edu/nauvoo/chord.html?id=$v\">$v</a>");
		else
			array_push($resa, "$v");
	}
	
	
	array_push($json, "<tr><td>" . implode("</td><td>", $resa) . "</td></tr>");


}
	echo implode("", $json);

echo "</table>";

?>

