<?php

/****
 * We must generate a JSON file following the structure below
 *
    {
        "marriageUnits":[
                {"id": 1234, "name":"MU1"},
                {"id": 1231, "name":"MU2"},
                {"id": 1232, "name":"MU3"},
                {"id": 1233, "name":"MU4"},
                {"id": 1235, "name":"MU5"},
                {"id": 1236, "name":"MU6"},
                {"id": 1237, "name":"MU7"},
                {"id": 1238, "name":"MU8"},
                {"id": 1239, "name":"MU9"},
                {"id": 1260, "name":"MU10"}

        ],
        "people":[
                {"source":1234,"target":1232, "gender":"M", "name": "Smith, John"},
                {"source":1231,"target":1232, "gender":"F", "name": "Jones, Mary"},
                {"source":1232,"target":1236, "gender":"M", "name": "Smith, Tom"},
                {"source":1233,"target":1236, "gender":"F", "name": "Bowls, Debra"},
                {"source":1235,"target":1236, "gender":"F", "name": "Carter, Rebekah"},
                {"source":1236,"target":1237, "gender":"F", "name": "Smith, Rachel"},
                {"source":1236,"target":1238, "gender":"M", "name": "Smith, Matthew"},
                {"source":1236,"target":1239, "gender":"F", "name": "Smith, Martha"},
                {"source":1236,"target":1260, "gender":"F", "name": "Smith, Christina"}
        ]
    }
 */


// Steps to take
// ==================
// 1. Query the database for the women that are in each husband's marriages (we can simplify the muliple queries below into one)
//    - These will be the in edges for each marriage unit
// 2. Query the database for the children that are in each husband's marriages
//    - These will be the out edges for each marriage unit
// 3. List each husband as the ID for the marriage unit and name as the MU name
// 4. For each in-edge of each MU, check to see if they are from another marriage we have found. If so, use that id as their
//       source.  If not, then create a dummy marriage with their last name as their source and add the marriage to the list.
//       NOTE: if two people come from the same marriage (ChildOfMarriageID DB entry will be helpful here, actually)
// 5. Ignore fixing up out edges.  In that case, we won't display the edge, as people don't have to get married, and we likely
//       don't have that data.  The in edges will cover all the cases of finding the relations in our data.

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

