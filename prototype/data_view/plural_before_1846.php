
<?php

//header('Content-type: application/json');

$id = 14;
if (isset($_GET["id"]))
	$id = $_GET["id"];
	

$db = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_data user=nauvoo password=p7qNpqygYU");

$result = pg_query($db, "SELECT DISTINCT  
    h.\"PersonID\" as \"HusbandID\", 
    hn.\"Last\" as \"HusbandLast\", hn.\"First\" as \"HusbandFirst\", w.\"PersonID\" as \"WifeID\", wn.\"Last\" as \"WifeLast\", wn.\"First\" as \"WifeFirst\", wp.\"DeathDate\" as \"WifeDeath\",
    m.\"Type\", m.\"MarriageDate\", m.\"DivorceDate\",m.\"CancelledDate\"
    FROM public.\"Marriage\" m, public.\"PersonMarriage\" h, public.\"PersonMarriage\" w, public.\"Name\" wn, public.\"Name\" hn, public.\"Person\" wp
    WHERE
    h.\"MarriageID\" = m.\"ID\" AND h.\"Role\" = 'Husband' AND w.\"MarriageID\" = m.\"ID\" AND w.\"Role\" = 'Wife' 
    AND wn.\"PersonID\" = w.\"PersonID\" AND wn.\"Type\" = 'authoritative'
    AND hn.\"PersonID\" = h.\"PersonID\" AND hn.\"Type\" = 'authoritative'
    AND wp.\"ID\" = w.\"PersonID\"
    ORDER BY h.\"PersonID\" ASC, m.\"MarriageDate\" ASC;");
if (!$result) {
    echo "1An error occurred.\n";
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
    if ($i > 0 && $arr[$i-1]["HusbandID"] == $mar["HusbandID"] && $mar["MarriageDate"] != "" && $mar["MarriageDate"] < "1845-12-10"
        && ($arr[$i-1]["WifeDeath"] <= $mar["MarriageDate"]) ) { // and if the marriage is after the previous wife's death
		foreach ($mar as $k=>$v) {
			//array_push($resa,"\"$k\": \"$v\"");
			if ($k == "HusbandID")
				array_push($resa, "<a href=\"http://ford.cs.virginia.edu/nauvoo/chord.html?id=$v\">$v</a>");
			else
				array_push($resa, "$v");
		}
	
		array_push($json, "<tr><td>" . implode("</td><td>", $resa) . "</td></tr>");

		$name = $mar["HusbandLast"] . ", ". $mar["HusbandFirst"];
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


