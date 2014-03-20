<?php

header('Content-type: application/json');

$id = 14;
if (isset($_GET["id"]))
	$id = $_GET["id"];
	

$db = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_data user=nauvoo password=p7qNpqygYU");

$result = pg_query($db, "SELECT * FROM public.\"Marriage\" WHERE \"ID\"=$id");
if (!$result) {
    echo "An error occurred.\n";
    exit;
}

$arr = pg_fetch_all($result);

// got the marriage
$marriage = $arr[0];


$result = pg_query($db, "SELECT * FROM public.\"Person\" WHERE \"ID\"=" . $marriage["HusbandID"]);
if (!$result) {
    echo "An error occurred.\n";
    exit;
}

$arr = pg_fetch_all($result);

// got the husband
$husband = $arr[0];


$result = pg_query($db, "SELECT * FROM public.\"Person\" WHERE \"ID\"=" . $marriage["WifeID"]);
if (!$result) {
    echo "An error occurred.\n";
    exit;
}

$arr = pg_fetch_all($result);

// got the wife
$wife = $arr[0];


$result = pg_query($db, "SELECT * FROM public.\"Person\" WHERE \"ChildOfMarriageID\"=" . $marriage["ID"]);
if (!$result) {
    echo "An error occurred.\n";
    exit;
}

$arr = pg_fetch_all($result);

// got the children
$children = $arr;

$parents = array($husband, $wife);

echo "{ \"parents\": [";
$parPrint = array();
foreach ($parents as $parent) {
	array_push($parPrint, "{ \"name\": \"" . $parent["Surname"] . ", " . $parent["GivenName"] . "\", \"gender\": \"". $parent["Gender"] ."\"}");
} 
echo implode(",", $parPrint);

echo "], \"children\": [";

$chiPrint = array();
foreach ($children as $child) {
	array_push($chiPrint, "{ \"name\": \"" . $child["Surname"] . ", " . $child["GivenName"] . "\", \"gender\": \"". $child["Gender"] ."\"}");
} 

echo implode(",", $chiPrint);

echo "], \"relationships\":[] }";


//print_r($arr);

?>