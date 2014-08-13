<?php

header('Content-type: application/json');

$id = 50;
if (isset($_GET["id"]))
	$id = $_GET["id"];
	

$db = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_new user=nauvoo password=p7qNpqygYU");

$result = pg_query($db, "SELECT * FROM public.\"Marriage\" WHERE \"HusbandID\"=$id ORDER BY \"MarriageDate\" ASC");
if (!$result) {
    echo "An error occurred.\n";
    exit;
}

$arr = pg_fetch_all($result);

// got the marriage
$marriages = $arr;

$parents = array();
$children = array();
$relations = array();

$result = pg_query($db, "SELECT * FROM public.\"Person\" WHERE \"ID\"=" . $marriages[0]["HusbandID"]);
if (!$result) {
    echo "An error occurred.\n";
    exit;
}

$arr = pg_fetch_all($result);

// got the husband
$arr[0]["Married"] = "";
$arr[0]["Divorced"] = "";
array_push($parents, $arr[0]);


// Get the wives and their children and adoptions to this wife
foreach ($marriages as $marriage) {
	$result = pg_query($db, "SELECT * FROM public.\"Person\" WHERE \"ID\"=" . $marriage["WifeID"]);
	if (!$result) {
	    echo "An error occurred.\n";
	    exit;
	}

	$arr = pg_fetch_all($result);

	// got the wife
	$wife = $arr[0];
	$wife["Married"] = $marriage["MarriageDate"];
	$wife["Divorced"] = $marriage["DivorceDate"];
	array_push($parents,$wife);


	$result = pg_query($db, "SELECT * FROM public.\"Person\" WHERE \"ChildOfMarriageID\"=" . $marriage["ID"]);
	if (!$result) {
	    echo "An error occurred.\n";
	    exit;
	}

	$arr = pg_fetch_all($result);

	// IDEA: Reverse the order of the children for each wife before adding them to the children array.  This should fix the chord diagram issues.

	$tmpchildren = array();
	// got the biological children
	foreach ($arr as $child) {
		$child["AdoptionDate"] = "";
		array_push($tmpchildren, $child);
		array_push($relations, "{\"desc\": \"Child Of\", \"type\":\"biological\", \"from\":\"" . $child["Surname"] . ", " . $child["GivenName"] . " (Child)\", \"to\":\"" . $wife["Surname"] . ", " . $wife["GivenName"] . " (Parent)\"}");
	}


	$result = pg_query($db, "SELECT \"Person\".*, \"Adoption\".\"AdoptionDate\" FROM public.\"Person\", public.\"Adoption\" WHERE \"Person\".\"ID\"=\"Adoption\".\"PersonID\" and \"Adoption\".\"MarriageID\"=" . $marriage["ID"]);
	if (!$result) {
	    echo "An error occurred.\n";
	    exit;
	}

	$arr = pg_fetch_all($result);

	// got the adopted children
	foreach ($arr as $child) {
		array_push($tmpchildren, $child);
		array_push($relations, "{\"desc\": \"Adopted To\", \"type\":\"adoption\", \"from\":\"" . $child["Surname"] . ", " . $child["GivenName"] . " (Child)\", \"to\":\"" . $wife["Surname"] . ", " . $wife["GivenName"] . " (Parent)\"}");
	}

	$children = array_merge($children, $tmpchildren);//array_reverse($tmpchildren));
}



echo "{ \"parents\": [";
$parPrint = array();
foreach ($parents as $parent) {
	array_push($parPrint, "{ \"name\": \"" . $parent["Surname"] . ", " . $parent["GivenName"] . " (Parent)\", ".
		"\"birthDate\":\"".$parent["BirthDate"]."\", \"deathDate\":\"".$parent["DeathDate"]."\", \"gender\": \"". $parent["Gender"] ."\", \"marriageDate\": \"".$parent["Married"]."\", \"divorceDate\":\"".$parent["Divorced"]."\"}");
} 
echo implode(",", $parPrint);

echo "], \"children\": [";

$chiPrint = array();
foreach ($children as $child) {
	array_push($chiPrint, "{ \"name\": \"" . $child["Surname"] . ", " . $child["GivenName"] . " (Child)\", ".
		"\"birthDate\":\"".$child["BirthDate"]."\", \"deathDate\":\"".$child["DeathDate"]."\", ".
		"\"gender\": \"". $child["Gender"] ."\", \"adoptionDate\": \"".$child["AdoptionDate"]."\"}");
} 

echo implode(",", $chiPrint);

echo "], \"relationships\": [ " . implode(",", $relations) ."] }";


//print_r($arr);

?>
