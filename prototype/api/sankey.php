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
// 6. We need to fix the out edges in the cases where they may go to the same out marriage.  That is, the children of two different
//       people may end up married in the end.  We need a way to approach this.

header('Content-type: application/json');

$ids = array( 626, 627, 54057, 60825, 59372, 634, 20792);
if (isset($_GET["id"]))
	$ids = explode(",",$_GET["id"]);

$marriageUnits = array();
$people = array();

$db = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_new user=nauvoo password=p7qNpqygYU");

// Insert this person with either source or target (direction) pointing to this id
function insertPerson($person, $direction, $id) {
    global $people, $marriageUnits;
    
    // If they are not already there, add them
    if (!isset($people[$person["ID"]]))
        $people[$person["ID"]] =  array("id"=>$person["ID"], "source"=>null, "target"=>null, "gender"=>$person["Gender"], "name"=>$person["Surname"] . ", " . $person["GivenName"], "childOf"=>$person["ChildOfMarriageID"]);
        
    // If they don't have a parent marriage, then set this field to -1
    if (!isset($person["ChildOfMarriageID"]) || $person["ChildOfMarriageID"] == "") {
            $people[$person["ID"]]["childOf"] = -1;
    }
    
    // If we have a boy, he gets his own MU with himself as target. 
    if ($person["Gender"] == "M") {
        if (!array_key_exists($person["ID"], $marriageUnits))
            $marriageUnits[$person["ID"]] =  array("id"=>$person["ID"], "name"=>$person["Surname"] . ", " . $person["GivenName"]);
        $people[$person["ID"]]["target"] = $person["ID"];
    }
    
    // Set the direction we had asked for to the proper id
    $people[$person["ID"]][$direction] = $id;
}


// For each husband id, get the wives
foreach($ids as $id) {
    // Get the husband's information    
    $result = pg_query($db, "SELECT * FROM public.\"Person\"  WHERE \"ID\"=$id");
    if (!$result) {
        echo "An error occurred.\n";
        exit;
    }
    $arr = pg_fetch_all($result);
    $husband = $arr[0];

    // set up this marriage unit ID as the husband's ID
    $marriageUnits[$id] = array("id"=>$id, "name"=>$husband["Surname"] . ", " . $husband["GivenName"]);
    insertPerson($husband, "target", $id);

    $result = pg_query($db, "SELECT * FROM public.\"Marriage\", public.\"Person\"  WHERE \"Person\".\"ID\" = \"Marriage\".\"WifeID\" AND \"Marriage\".\"HusbandID\"=$id ORDER BY \"MarriageDate\" ASC");
    if (!$result) {
        echo "An error occurred.\n";
        exit;
    }
    $arr = pg_fetch_all($result);
    //print_r($arr);
    foreach($arr as $wife) {
        // Try to get the parent if ChildOfMarriageID is set to something.  May also try to do it inside of insertPerson.

        insertPerson($wife, "target", $id);    
    }
}

// For each husband id, get all the children
foreach($ids as $id) {
    
    $result = pg_query($db, "SELECT \"Person\".*, \"Marriage\".\"HusbandID\" as \"Father\" FROM public.\"Marriage\", public.\"Person\"  WHERE \"Person\".\"ChildOfMarriageID\" = \"Marriage\".\"ID\" AND \"Marriage\".\"HusbandID\"=$id ORDER BY \"Person\".\"BirthDate\" ASC");
    if (!$result) {
        echo "An error occurred.\n";
        exit;
    }
    $arr = pg_fetch_all($result);
    //print_r($arr);
    
    // Do things for each child
    foreach($arr as $child) {
            insertPerson($child, "source", $id);
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
            if ($person["gender"] == "F") {
                $result = pg_query($db, "SELECT \"Marriage\".\"HusbandID\" FROM public.\"Marriage\"  WHERE \"WifeID\"={$person['id']}");
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

