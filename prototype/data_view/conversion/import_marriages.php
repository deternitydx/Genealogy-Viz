<?php
include_once("common_functions.php");

$DEBUG = false;
$db_to = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_data user=nauvoo password=p7qNpqygYU");

$csvfile = fopen("marriages_dups.csv", "r");
$badfile = fopen("failed_marriages_dups.csv", "w");
$newidfile = fopen("marriages_newid_dups.csv", "w");
if ($csvfile == NULL)
        die("Error reading file");
$head = fgetcsv($csvfile);
fputcsv($badfile, $head);
fputcsv($newidfile, $head);
foreach ($head as $k => $v) {
        $head[$k] = trim($v);
}
$headrev = array_flip($head);
$data1 = fgetcsv($csvfile);
$knownIds = array(); // indexed BYUID => UVAID
$types = array("Eternal" => "eternity", "Time" => "time", "Civil" => "civil", "Unknown" => "unknown");
$typesrev = array_flip($types);
$currentID = 100000;
while ($data1 !== false) {
        $data = array();
        foreach ($data1 as $k => $v) 
                $data[$head[$k]] = trim($v);
        echo "Read one line: {$data["Male Household ID"]} = {$data["Person Sealed to Household Male ID"]}\n";

        // Full data is now available in data by text indexing

        // Ignore the row if it is specified to be skipped
        if (isset($data["Skip"]) && $data["Skip"] == "Y") {
                echo "       Skipped this row.\n";
                // Grab the next line of the file
                write_bad_marriage($badfile, $data1);
                $data1 = fgetcsv($csvfile);
                continue;
        }
        // Ignore the row if it is the husband
        if (isset($data["Root Husband"]) && $data["Root Husband"] == "Y") {
                echo "       Ignoring Root Husband\n";
                // Grab the next line of the file
                write_bad_marriage($badfile, $data1);
                $data1 = fgetcsv($csvfile);
                continue;
        }

        // Put the information into its own structure
        $marriage = array();

        // Look up the husband id in the UVA database
        if ($data["Male Household ID"] != '' && !isset($knownIds[$data["Male Household ID"]])
                && is_numeric($data["Male Household ID"])) { // look up husband id
                $byuid = $data["Male Household ID"];
                $result = pg_query($db_to, "SELECT * FROM public.\"Person\" WHERE \"BYUID\"={$byuid} ORDER BY \"ID\" asc");
                if (!$result) {
                        echo "An error occurred.\n";
                        exit;
                }

                $row = pg_fetch_array($result);
                //print_r($row);
                if (!isset($row["ID"]) || $row["ID"] == null) {
                        echo "Could NOT find husband with id $byuid in the database.";
                        die();
                }
                $knownIds[$byuid] = $row["ID"];

                echo "Added BYUID $byuid into our known list of ids (as {$knownIds[$byuid]})\n";
        } else if ($data["Male Household ID"] == "" || !is_numeric($data["Male Household ID"])) {
                echo "*****Missing husband for row.\n";
                // Grab the next line of the file
                write_bad_marriage($badfile, $data1);
                $data1 = fgetcsv($csvfile);
                continue;
        }

        $marriage["HusbandID"] = $knownIds[$data["Male Household ID"]];


        // Look up the wife id in the UVA database
        if ($data["Person Sealed to Household Male ID"] != '' 
                && !isset($knownIds[$data["Person Sealed to Household Male ID"]])) { // look up wife ID
                $byuid = $data["Person Sealed to Household Male ID"];
                $result = pg_query($db_to, "SELECT * FROM public.\"Person\" WHERE \"BYUID\"={$byuid} ORDER BY \"ID\" asc");
                if (!$result) {
                        echo "An error occurred.\n";
                        exit;
                }

                $row = pg_fetch_array($result);
                //print_r($row);
                $knownIds[$byuid] = $row["ID"];
                echo "Added BYUID $byuid into our known list of ids\n";
        } else if (isset($knownIds[md5($data["First Name"] . $data["Middle Name"] . $data["Last Name"])])) { 
                // person has been added already
                $data["Person Sealed to Household Male ID"] = md5($data["First Name"] . $data["Middle Name"] . $data["Last Name"]);
        } else if (!isset($knownIds[$data["Person Sealed to Household Male ID"]])){
                echo "Missing wife for row.  Adding her to the database\n";
                $arr = array ("Gender" => "Female");
                $id = "";

                $insert = get_insert_statement("Person", $arr);
                if (!$DEBUG) {
                        $res = pg_query($db_to, $insert);
                }
                if ($DEBUG || $res) {
                        if (!$DEBUG) {
                            $res = pg_query($db_to, "SELECT lastval()");
                            $temprow = pg_fetch_Array($res);

                            // grab this person's ID in the new database
                            $id = $temprow[0];

                            // insert the person's name as an authoritative name
                            $arr = array ("PersonID" => $id,
                                "First" => pg_escape_string($data['First Name']),
                                "Middle" => pg_escape_string($data['Middle Name']),
                                "Last" => pg_escape_string($data['Last Name']),
                                "Type" => "authoritative");
                            $insert = get_insert_statement("Name", $arr);
                            $res = pg_query($db_to, $insert);
                        } else {
                            $id = "DEBUG";
                        }

                }
                //print_r ($data);
                $currentID = md5($data["First Name"] . $data["Middle Name"] . $data["Last Name"]);
                $knownIds[$currentID] = $id;
                $data["Person Sealed to Household Male ID"] = $currentID;
                echo "Successfully added new woman to database\n";
        }

        $marriage["WifeID"] = $knownIds[$data["Person Sealed to Household Male ID"]];

        // Look up the officiator ids in the UVA database
        $tmp = array();
        if ($data["Marriage Officiator"] != '' && !isset($knownIds[$data["Marriage Officiator"]])) { // look up wife ID
                $list = explode(";", $data["Marriage Officiator"]);
                foreach ($list as $byuid) {
                        $byuid = trim($byuid);
                        if ($byuid != "" && $byuid != "NR" && $byuid != "N/A" && is_numeric($byuid)) {
                                $result = pg_query($db_to, "SELECT * FROM public.\"Person\" WHERE \"BYUID\"={$byuid} ORDER BY \"ID\" asc");
                                if (!$result) {
                                        echo "An error occurred.\n";
                                        exit;
                                }

                                $row = pg_fetch_array($result);
                                //print_r($row);
                                $knownIds[$byuid] = $row["ID"];
                                echo "Added BYUID $byuid into our known list of ids\n";
                                array_push($tmp, $row["ID"]);
                        }
                }
        }
        if (!empty($tmp))
                $marriage["OfficiatorIDs"] = $tmp;

        // Look up the husband proxy ids in the UVA database
        $tmp = array();
        if ($data["MP Male"] != '' && !isset($knownIds[$data["MP Male"]])) { // look up husband proxy
                $list = explode(";", $data["MP Male"]);
                foreach ($list as $byuid) {
                        $byuid = trim($byuid);
                        if ($byuid != "" && $byuid != "NR" && $byuid != "N/A" && is_numeric($byuid)) {
                                $result = pg_query($db_to, "SELECT * FROM public.\"Person\" WHERE \"BYUID\"={$byuid} ORDER BY \"ID\" asc");
                                if (!$result) {
                                        echo "An error occurred.\n";
                                        exit;
                                }

                                $row = pg_fetch_array($result);
                                //print_r($row);
                                $knownIds[$byuid] = $row["ID"];
                                echo "Added BYUID $byuid into our known list of ids\n";
                                array_push($tmp, $row["ID"]);
                        }
                }
        }
        if (!empty($tmp))
                $marriage["HusbandProxyIDs"] = $tmp;

        // Look up the wife proxy ids in the UVA database
        $tmp = array();
        if ($data["MP Female"] != '' && !isset($knownIds[$data["MP Female"]])) { // look up husband proxy
                $list = explode(";", $data["MP Female"]);
                foreach ($list as $byuid) {
                        $byuid = trim($byuid);
                        if ($byuid != "" && $byuid != "NR" && $byuid != "N/A" && is_numeric($byuid)) {
                                $result = pg_query($db_to, "SELECT * FROM public.\"Person\" WHERE \"BYUID\"={$byuid} ORDER BY \"ID\" asc");
                                if (!$result) {
                                        echo "An error occurred.\n";
                                        exit;
                                }

                                $row = pg_fetch_array($result);
                                //print_r($row);
                                $knownIds[$byuid] = $row["ID"];
                                echo "Added BYUID $byuid into our known list of ids\n";
                                array_push($tmp, $row["ID"]);
                        }
                }
        }
        if (!empty($tmp))
                $marriage["WifeProxyIDs"] = $tmp;

        // Look up the marriage ID in the UVA Database
        if ($data["Marriage ID (BYU)"] != '') { // look up wife ID
                $byuid = $data["Marriage ID (BYU)"];
                $result = pg_query($db_to, "SELECT * FROM public.\"Marriage\" WHERE \"BYUID\"={$byuid} ORDER BY \"ID\" asc");
                if (!$result) {
                        echo "An error occurred.\n";
                        exit;
                }

                $row = pg_fetch_array($result);
                //print_r($row);
                $marriage["ID"] = $row["ID"];
                $marriage["BYUID"] = $byuid;
                echo "Used BYUMarriageID $byuid for marriage\n";
        }

        // Look up the type
        $tmp = $data["Marriage Type (Civil, Time, Eternal)"];
        if (in_array($tmp, array_keys($types)))
            $marriage['Type'] = $types[$tmp];
        else
            $marriage["Type"] = "unknown";

        // Set whether or not this is a root marriage
        if ($data["Root Wife"] == "Y")
                $marriage["Root"] = "true";
        else
                $marriage["Root"] = "false";

        // Set the marriage date
        if ($data["Marriage Date"] != '' && preg_match("/^[0-9]{4}-?[0-9]{0,2}-?[0-9]{0,2}$/", $data["Marriage Date"]))
                $marriage['MarriageDate'] = $data["Marriage Date"];

        // Set the Divorce Date
        if ($data["Divorce"] != '')
                $marriage['DivorceDate'] = $data["Divorce"];

        // Set the notes field
        $marriage["Notes"] = pg_escape_string($data["Notes"]);

        // Debugging print statement
        //print_r($marriage);
            echo $tmp . " == " . $marriage["Type"] . "\n";

        $arr = array(   "Root" => $marriage['Root'],
                        "Type" => $marriage['Type'],
                        "PrivateNotes" => $marriage["Notes"]);
        if (isset($marriage["MarriageDate"]))
                $arr["MarriageDate"] = $marriage["MarriageDate"];
        if (isset($marriage["DivorceDate"]))
                $arr["DivorceDate"] = $marriage["DivorceDate"];
        // Update the marriage, if it exists
        if (isset($marriage['ID'])) { // we have a marriage already in the database!

                $update = get_update_statement("Marriage", $arr, "\"ID\"={$marriage['ID']}");
                if (!$DEBUG) {
                        $result = pg_query($db_to, $update);
                        if (!$result) die("Error updating marriage");
                } else { echo "$update\n"; }
        } else { // no marriage, so we must insert!
                $insert = get_insert_statement("Marriage", $arr);
                if (!$DEBUG) {
                        $result = pg_query($db_to, $insert);
                        if (!$result) die("Error inserting marriage");

                        // Get the last value (marriage id)
                        $res = pg_query($db_to, "SELECT lastval()");
                        $temprow = pg_fetch_Array($res);

                        // grab this marriage's ID in the new database
                        $marriage['ID'] = $temprow[0];
                } else { echo "$insert\n"; $marriage['ID'] = "DEBUG"; }
        }

        // Create the list of people in this table
        $people = array ();
        array_push($people, array("PersonID" => $marriage["HusbandID"], "Role" => "Husband"));
        array_push($people, array("PersonID" => $marriage["WifeID"], "Role" => "Wife"));
        if (isset($marriage["OfficiatorIDs"]))
                foreach (array_unique($marriage["OfficiatorIDs"]) as $tmpid)
                    array_push($people, array("PersonID" => $tmpid, "Role" => "Officiator"));
        if (isset($marriage["WifeProxyIDs"]))
                foreach (array_unique($marriage["WifeProxyIDs"]) as $tmpid)
                    array_push($people, array("PersonID" => $tmpid, "Role" => "ProxyWife"));
        if (isset($marriage["HusbandProxyIDs"]))
                foreach (array_unique($marriage["HusbandProxyIDs"]) as $tmpid)
                    array_push($people, array("PersonID" => $tmpid, "Role" => "ProxyHusband"));

        // First, Remove the one that is in the database, if it exists
        $query = "DELETE FROM \"PersonMarriage\" WHERE \"MarriageID\"={$marriage['ID']}";
        if (!$DEBUG) {
                $res = pg_query($db_to, $query);
        } else { echo "$query\n"; }

        // Then, insert husband, wife, proxies (if exist), officiator (if exist) into the personmarriage table
        foreach ($people as $person) {
                // Insert each person
                $arr = array_merge($person, array("MarriageID" => $marriage['ID']));
                $insert = get_insert_statement("PersonMarriage", $arr);
                if (!$DEBUG) {
                        $result = pg_query($db_to, $insert);
                        if (!$result) die("Error inserting person-marriage");
                } else { echo "$insert\n"; }


        }

        write_good_marriage($newidfile, $data1, $marriage);
        // Grab the next line of the file
        $data1 = fgetcsv($csvfile);
}

fclose($csvfile);
fclose($badfile);
fclose($newidfile);

function write_bad_marriage($file, $line) {
    fputcsv($file, $line);
}

function write_good_marriage($file, $line, $mg) {
        global $headrev; global $typesrev;

        $line[$headrev["Marriage ID (BYU)"]] = $mg["ID"];
        $line[$headrev["Male Household ID"]] = $mg["HusbandID"];
        $line[$headrev["Person Sealed to Household Male ID"]] = $mg["WifeID"];
        if (isset($mg["OfficiatorIDs"]))
            $line[$headrev["Marriage Officiator"]] = implode(";", $mg["OfficiatorIDs"]);
        else 
            $line[$headrev["Marriage Officiator"]] = "";
        if (isset($mg["HusbandProxyIDs"]))
            $line[$headrev["MP Male"]]  = implode(";", $mg["HusbandProxyIDs"]);
        else 
            $line[$headrev["MP Male"]] = "";
        if (isset($mg["WifeProxyIDs"]))
        $line[$headrev["MP Female"]]  = implode(";", $mg["WifeProxyIDs"]);
        else 
            $line[$headrev["MP Female"]] = "";
        $line[$headrev["Marriage Type (Civil, Time, Eternal)"]] = $typesrev[$mg["Type"]];

        fputcsv($file, $line);
}
?>
