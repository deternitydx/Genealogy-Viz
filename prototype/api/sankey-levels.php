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
//       NOTE: if two people come from the same marriage (BiologicalChildOfMarriage DB entry will be helpful here, actually)
// 5. Ignore fixing up out edges.  In that case, we won't display the edge, as people don't have to get married, and we likely
//       don't have that data.  The in edges will cover all the cases of finding the relations in our data.
// 6. We need to fix the out edges in the cases where they may go to the same out marriage.  That is, the children of two different
//       people may end up married in the end.  We need a way to approach this.


// LEVELS Steps to take
//
// After adding everyone, but before adding the dummy nodes, put everyone without a source into the left edge and everyone 
// without a target to the right edge.  For each left-edge, look up parent's marriage and participants and add.  For each
// right-edge, look up children's marriage and participants to add.  Attach appropriately.  
//  -- repeat for each level (add to edge, process edges)

header('Content-type: application/json');

$ids = array( 615, 616, 51049);
if (isset($_GET["id"]))
    $ids = explode(",",$_GET["id"]);
$levels = 0;
if (isset($_GET["levels"]))
    $levels = $_GET["levels"];
$showall = false;
if (isset($_GET["showall"]))
    $showall = true;

$marriageUnits = array();
$people = array();

$db = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_data user=nauvoo password=p7qNpqygYU");

// Insert this person with either source or target (direction) pointing to this id
function insertPerson($person, $direction, $id) {
    global $people, $marriageUnits;
    
    // If they are not already there, add them
    if (!isset($people[$person["ID"]]))
        $people[$person["ID"]] =  array("id"=>$person["ID"], "source"=>null, "target"=>null, "gender"=>$person["Gender"], "name"=>$person["Last"] . ", " . $person["First"] . " " . $person["Middle"], "childOf"=>$person["BiologicalChildOfMarriage"]);
        
    // If they don't have a parent marriage, then set this field to -1
    if (!isset($person["BiologicalChildOfMarriage"]) || $person["BiologicalChildOfMarriage"] == "") {
            $people[$person["ID"]]["childOf"] = -1;
    }
    
    // If we have a boy, he gets his own MU with himself as target. 
    if ($person["Gender"] == "Male") {
        if (!array_key_exists($person["ID"], $marriageUnits))
            $marriageUnits[$person["ID"]] =  array("id"=>$person["ID"], "name"=>$person["Last"] . ", " . $person["First"] . " " . $person["Middle"]);
        $people[$person["ID"]]["target"] = $person["ID"];
    }
    
    // Set the direction we had asked for to the proper id
    $people[$person["ID"]][$direction] = $id;
}


function processID($id) {
    // Get the husband's information    
    $result = pg_query($db, "SELECT * FROM public.\"Person\" p, public.\"Name\" n  WHERE p.\"ID\"=$id
         AND p.\"ID\" = n.\"PersonID\" AND n.\"Type\"='authoritative'");
    if (!$result) {
        echo "An error occurred.\n";
        exit;
    }
    $arr = pg_fetch_all($result);
    $husband = $arr[0];

    // set up this marriage unit ID as the husband's ID
    $marriageUnits[$id] = array("id"=>$id, "name"=>$husband["Last"] . ", " . $husband["First"] . " " . $husband["Middle"]);
    insertPerson($husband, "target", $id);

    // Get the wives for this husband
    $result = pg_query($db, "select distinct p.\"ID\", n.\"First\", n.\"Middle\", n.\"Last\", m.\"MarriageDate\", p.\"Gender\", p.\"BiologicalChildOfMarriage\" from (select pm.* from \"PersonMarriage\" pm where pm.\"PersonID\"=$id and pm.\"Role\" = 'Husband') mid, \"PersonMarriage\" pmw, \"Person\" p, \"Marriage\" m, \"Name\" n where mid.\"MarriageID\"=pmw.\"MarriageID\" and pmw.\"Role\" = 'Wife' and n.\"PersonID\" = pmw.\"PersonID\" and pmw.\"PersonID\" = p.\"ID\" and n.\"Type\" = 'authoritative' and m.\"ID\" = pmw.\"MarriageID\" order by m.\"MarriageDate\" ASC;");
    if (!$result) {
        echo "An error occurred.\n";
        exit;
    }
    $arr = pg_fetch_all($result);
    foreach($arr as $wife) {
        insertPerson($wife, "target", $id);    
    }

    // Get the children for this man's marriage 
    $result = pg_query($db, "select distinct p.\"ID\", n.\"First\", n.\"Middle\", n.\"Last\", p.\"Gender\", p.\"BiologicalChildOfMarriage\", p.\"BirthDate\", p.\"DeathDate\" from (select pm.\"MarriageID\" from \"PersonMarriage\" pm where pm.\"PersonID\"=$id and pm.\"Role\" = 'Husband') mid, \"Person\" p, \"Name\" n where mid.\"MarriageID\"=p.\"BiologicalChildOfMarriage\" and n.\"PersonID\" = p.\"ID\" and n.\"Type\" = 'authoritative' order by p.\"BirthDate\" ASC;");
    if (!$result) {
        echo "An error occurred.\n";
        exit;
    }
    $arr = pg_fetch_all($result);
    
    // Do things for each child
    foreach($arr as $child) {
            insertPerson($child, "source", $id);
    }

}

// **********************************************************************************************************


// For each husband id, get the wives
foreach($ids as $id) {
    processID($id);
}


// For each level, we'll check edges and look for more people
for ($curlevel = 0; $curlevel < $levels; $curlevel++) {
    $leftedge = array();
    $rightedge = array();
    foreach($people as $i => $person) {
        if ($person["source"] === null)
            array_push($leftedge, $i);
        if ($person["target"] === null)
            array_push($rightedge, $i);
    }

    foreach ($leftedge as $i) {
        // Look up parental marriage's husband id and get parents
        if ($people[$i]["childOf"] != -1)  { // we know they have a parent
            // Start by getting all parents in that marriage
            $mid = $people[$id]["childOf"];
            $result = pg_query($db, "select distinct p.\"ID\", n.\"First\", n.\"Middle\", n.\"Last\", m.\"MarriageDate\", p.\"Gender\", p.\"BiologicalChildOfMarriage\" from (select pm.* from \"PersonMarriage\" pm where pm.\"MarriageID\"=$mid) mid,\"Person\" p, \"Name\" n, \"Marriage\" m where mid.\"PersonID\"=p.\"ID\" and n.\"PersonID\" = mid.\"PersonID\" and n.\"Type\" = 'authoritative' and mid.\"MarriageID\"=m.\"ID\" order by m.\"MarriageDate\" ASC;");
            if (!$result) {
                 echo "An error occurred.\n";
                 exit;
            }
            $arr = pg_fetch_all($result);
            foreach($arr as $parent) {
                // this doesn't work for this particular application
                // insertPerson($parent, "target", $id);    
            }
            
        }
    }

    foreach ($rightedge as $i) {
        // Look up child marriage's children
    }
}


/****
 * Clean up and add dummy nodes to those we don't know about
 */
$dummyID = 1000000;
$known = array();
foreach($people as $i => $person) {
        if ($person["source"] === null) {
            if ($person["childOf"] != -1 && array_key_exists($person["childOf"], $known))
                $people[$i]["source"] = $known[$person["childOf"]];
            else {
                $marriageUnits[$dummyID] = array("id"=>$dummyID, "name"=>"");
                $people[$i]["source"] = $dummyID;
                $known[$person["childOf"]] = $dummyID;
                $dummyID++;
            }
        }

        if ($person["target"] === null) {
            $needDummy = true;
            // check to see if woman, and if so, then let's query to see if she's married one of the men we have
            if ($person["gender"] == "Female") {
                $result = pg_query($db, "SELECT pm.\"PersonID\" as \"HusbandID\" FROM (SELECT m.\"MarriageID\" FROM public.\"PersonMarriage\" as m  WHERE \"PersonID\"={$person['id']} AND \"Role\" = 'Wife') m, \"PersonMarriage\" pm WHERE pm.\"MarriageID\" = m.\"MarriageID\" and pm.\"Role\" = 'Husband';");
                if (!$result) {
                    echo "An error occurred.\n";
                    exit;
                }
                $arr = pg_fetch_all($result);
                $husbandID = null;
                foreach ($arr as $target) {
						$husbandID = $target["HusbandID"];
                        if (array_key_exists($husbandID, $marriageUnits)) {
                                $people[$i]["target"] = $husbandID;
                                $needDummy = false;
                        }
                }
                // If there is a husband ID, let's use his id as the target to catch some other women
                if ($needDummy && $husbandID != null) {
                	$marriageUnits[$husbandID] = array("id"=>$husbandID, "name"=>"");
                	$people[$i]["target"] = $husbandID;
                	$needDummy = false;
            	}
            }
            
            
            if ($needDummy) {
                $marriageUnits[$dummyID] = array("id"=>$dummyID, "name"=>"");
                $people[$i]["target"] = $dummyID;
                $dummyID++;
            }
        }
}

echo "{ \"marriageUnits\":[";

$i = 0;
foreach ($marriageUnits as $unit) {
        echo "{ \"id\":" . $unit["id"] . ", \"name\":\"" . $unit["name"] . "\"}";
        if ($i++ < count($marriageUnits) -1) echo ",";
}

echo "], \"people\": [";

$i = 0;
foreach ($people as $person) {
        echo "{ \"id\":" . $person["id"] . ", \"name\":\"" . $person["name"] . "\", \"source\":" . $person["source"]. ", \"target\":" .$person["target"] .", \"gender\":\"".$person["gender"] ."\", \"childOf\":\"".$person["childOf"]."\"}";
        if ($i++ < count($people) -1) echo ",";
}

echo "]}";
//print_r($marriageUnits);
//print_r($people);


?>

