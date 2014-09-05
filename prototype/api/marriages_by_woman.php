<?php

header('Content-type: application/json');

$id = 50;
if (isset($_GET["id"]))
	$id = $_GET["id"];
	

$db = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_data user=nauvoo password=p7qNpqygYU");

$result = pg_query($db, "SELECT m.\"ID\", m.\"PlaceID\", m.\"MarriageDate\", m.\"DivorceDate\",m.\"CancelledDate\", w.\"PersonID\" as \"WifeID\", h.\"PersonID\" as \"HusbandID\" FROM public.\"Marriage\" m, public.\"PersonMarriage\" h, public.\"PersonMarriage\" w WHERE
       h.\"MarriageID\" = m.\"ID\" AND h.\"Role\" = 'Husband' AND w.\"MarriageID\" = m.\"ID\" AND w.\"Role\" = 'Wife' AND w.\"PersonID\"=$id ORDER BY m.\"MarriageDate\" ASC");
if (!$result) {
    print_empty("Error finding marriages.");
    exit;
}

$arr = pg_fetch_all($result);

// got the marriage
$marriages = $arr;

$parents = array();
$children = array();
$relations = array();

$result = pg_query($db, "SELECT * FROM public.\"Person\" p, public.\"Name\" n WHERE p.\"ID\" = n.\"PersonID\" AND n.\"Type\" = 'authoritative' AND p.\"ID\"=" . $marriages[0]["WifeID"]);
if (!$result) {
    print_empty("No marriages with this woman.");
    exit;
}

$arr = pg_fetch_all($result);

// got the husband
$arr[0]["Married"] = "";
$arr[0]["Divorced"] = "";
array_push($parents, $arr[0]);


// Get the husbands and their children and adoptions to this wife
foreach ($marriages as $marriage) {
    $result = pg_query($db, "SELECT DISTINCT * FROM public.\"Person\" p, public.\"Name\" n WHERE p.\"ID\" = n.\"PersonID\" AND n.\"Type\" = 'authoritative' AND p.\"ID\"=" . $marriage["HusbandID"]);
	if (!$result) {
        print_empty("Error finding husband information.");
	    exit;
	}

	$arr = pg_fetch_all($result);

	// got the wife
	$wife = $arr[0];
	$wife["Married"] = $marriage["MarriageDate"];
	$wife["Divorced"] = $marriage["DivorceDate"];
	array_push($parents,$wife);


    $result = pg_query($db, "SELECT DISTINCT * FROM public.\"Person\" p, public.\"Name\" n WHERE p.\"ID\" = n.\"PersonID\" AND n.\"Type\" = 'authoritative' AND p.\"BiologicalChildOfMarriage\"=" . $marriage["ID"]);
	if (!$result) {
        print_empty("Error finding biological children.");
	    exit;
	}

	$arr = pg_fetch_all($result);


	$tmpchildren = array();
	// got the biological children
	foreach ($arr as $child) {
		$child["AdoptionDate"] = "";
		array_push($tmpchildren, $child);
		array_push($relations, "{\"desc\": \"Child Of\", \"type\":\"biological\", \"from\":\"" . $child["ID"] . "\", \"to\":\"" . $wife["ID"] . "\"}");
	}

	$children = array_merge($children, $tmpchildren);//array_reverse($tmpchildren));
}

//reorder the children by birthday
$births = array();
foreach ($children as $k => $child)
    $births[$k] = $child["BirthDate"];
array_multisort($births, $children);


echo "{ \"parents\": [";
$parPrint = array();
foreach ($parents as $parent) {
	array_push($parPrint, "{ \"id\": \"{$parent["ID"]}\", \"name\": \"" . $parent["Last"] . ", " . $parent["First"] . "\", ".
		"\"birthDate\":\"".$parent["BirthDate"]."\", \"deathDate\":\"".$parent["DeathDate"]."\", \"gender\": \"". $parent["Gender"] ."\", \"marriageDate\": \"".$parent["Married"]."\", \"divorceDate\":\"".$parent["Divorced"]."\"}");
} 
echo implode(",", $parPrint);

echo "], \"children\": [";

$chiPrint = array();
foreach ($children as $child) {
	array_push($chiPrint, "{ \"id\": \"{$child["ID"]}\", \"name\": \"" . $child["Last"] . ", " . $child["First"] . "\", ".
		"\"birthDate\":\"".$child["BirthDate"]."\", \"deathDate\":\"".$child["DeathDate"]."\", ".
		"\"gender\": \"". $child["Gender"] ."\", \"adoptionDate\": \"".$child["AdoptionDate"]."\"}");
} 

echo implode(",", $chiPrint);

echo "], \"relationships\": [ " . implode(",", $relations) ."] }";


//print_r($arr);
function print_empty($error) {
    echo "{ \"error\" : \"$error\", ";
    echo " \"parents\": [";
    echo "], \"children\": [";
    echo "], \"relationships\": [ ] }";
}

?>

