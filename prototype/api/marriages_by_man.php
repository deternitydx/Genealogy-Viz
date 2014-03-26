<?php

header('Content-type: application/json');

$id = 50;
if (isset($_GET["id"]))
	$id = $_GET["id"];
	

$db = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_new user=nauvoo password=p7qNpqygYU");

$result = pg_query($db, "SELECT * FROM public.\"Marriage\" WHERE \"HusbandID\"=$id");
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
array_push($parents, $arr[0]);

foreach ($marriages as $marriage) {
	$result = pg_query($db, "SELECT * FROM public.\"Person\" WHERE \"ID\"=" . $marriage["WifeID"]);
	if (!$result) {
	    echo "An error occurred.\n";
	    exit;
	}

	$arr = pg_fetch_all($result);

	// got the wife
	$wife = $arr[0];
	array_push($parents,$wife);


	$result = pg_query($db, "SELECT * FROM public.\"Person\" WHERE \"ChildOfMarriageID\"=" . $marriage["ID"]);
	if (!$result) {
	    echo "An error occurred.\n";
	    exit;
	}

	$arr = pg_fetch_all($result);

	// got the children
	foreach ($arr as $child) {
		array_push($children, $child);
		array_push($relations, "{\"desc\": \"Child Of\", \"type\":\"childOf\", \"from\":\"" . $child["Surname"] . ", " . $child["GivenName"] . " (Child)\", \"to\":\"" . $wife["Surname"] . ", " . $wife["GivenName"] . " (Parent)\"}");
	}

}

echo "{ \"parents\": [";
$parPrint = array();
foreach ($parents as $parent) {
	array_push($parPrint, "{ \"name\": \"" . $parent["Surname"] . ", " . $parent["GivenName"] . " (Parent)\", \"gender\": \"". $parent["Gender"] ."\"}");
} 
echo implode(",", $parPrint);

echo "], \"children\": [";

$chiPrint = array();
foreach ($children as $child) {
	array_push($chiPrint, "{ \"name\": \"" . $child["Surname"] . ", " . $child["GivenName"] . " (Child)\", \"gender\": \"". $child["Gender"] ."\"}");
} 

echo implode(",", $chiPrint);

echo "], \"relationships\": [ " . implode(",", $relations) ."] }";


//print_r($arr);

?>

