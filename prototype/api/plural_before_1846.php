
<?php

//header('Content-type: application/json');

$id = 14;
if (isset($_GET["id"]))
	$id = $_GET["id"];
	

$db = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_new user=nauvoo password=p7qNpqygYU");

$result = pg_query($db, "
SELECT 
  \"Marriage\".\"ID\", 
  \"Marriage\".\"HusbandID\", 
  \"Marriage\".\"WifeID\", 
  \"Marriage\".\"MarriageDateSearchable\", 
  \"Marriage\".\"MarriagePlace\", 
  \"Person\".\"Surname\", 
  \"Person\".\"GivenName\", 
  \"Person\".\"BirthDateSearchable\", 
  \"Person\".\"DeathDateSearchable\"
FROM 
  public.\"Marriage\", 
  public.\"Person\"
WHERE 
  \"Marriage\".\"HusbandID\" = \"Person\".\"ID\"
ORDER BY \"Marriage\".\"HusbandID\" asc, \"Marriage\".\"MarriageDateSearchable\" asc;");


if (!$result) {
    echo "An error occurred.\n";
    exit;
}

$arr = pg_fetch_all($result);
$names = array();
$json = array();
$head = array();
echo "<table border='1'>";
//echo "<tr><td>ID</td><td>Surname</td><td>Given Name</td><td>Birth Date</td><td>Death Date</td><td>Number of Wives</td></tr>";
foreach ($arr[0] as $k=>$v) 
	array_push($head, $k);
array_push($json, "<tr style='font-weight: bold;'><td>".implode("</td><td>", $head) . "</td></tr>");

foreach ($arr as $i => $mar) {
	$resa = array();

	// only add if the second marriage is < 1846
	if ($i > 0 && $arr[$i-1]["HusbandID"] == $mar["HusbandID"] && $mar["MarriageDateSearchable"] != "" && $mar["MarriageDateSearchable"] < "1845-12-10") {
		foreach ($mar as $k=>$v) {
			//array_push($resa,"\"$k\": \"$v\"");
			if ($k == "HusbandID")
				array_push($resa, "<a href=\"http://ford.cs.virginia.edu/nauvoo/chord.html?id=$v\">$v</a>");
			else
				array_push($resa, "$v");
		}
	
		array_push($json, "<tr><td>" . implode("</td><td>", $resa) . "</td></tr>");

		$name = $mar["Surname"] . ", ". $mar["GivenName"];
		if (!in_array($name, $names))
			array_push($names, $name);
	
	}

}
	echo implode("", $json);

echo "</table>";

sort($names);
foreach ($names as $name)
	echo "<br>$name";
echo "<br>".count($names);
?>


