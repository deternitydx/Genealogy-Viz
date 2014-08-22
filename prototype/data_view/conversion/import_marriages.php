<?php

$db_to = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_data user=nauvoo password=p7qNpqygYU");

$csvfile = fopen("marriages.csv", "r");
if ($csvfile == NULL)
        die("Error reading file");
$head = fgetcsv($csvfile);
$data1 = fgetcsv($csvfile);
$knownIds = array(); // indexed BYUID => UVAID
$types = array("Eternal" => "eternity", "Time" => "time", "Civil" => "civil");
$currentID = 100000;
while ($data1 !== false) {
       $data = array();
       foreach ($data1 as $k => $v) 
               $data[$head[$k]] = $v;

       // Full data is now available in data by text indexing

       // Ignore the row if it is the husband
       if (isset($data["Root Husband"]) && $data["Root Husband"] == "Y")
               continue;

       // Put the information into its own structure
       $marriage = array();

       // Look up the husband id in the UVA database
       if ($data["Male Household ID"] != '' && !isset($knownIds[$data["Male Household ID"]])) { // look up husband id
            $byuid = $data["Male Household ID"];
            $result = pg_query($db_to, "SELECT * FROM public.\"Person\" WHERE \"BYUID\"={$byuid} ORDER BY \"ID\" asc");
            if (!$result) {
                echo "An error occurred.\n";
                exit;
            }

            $row = pg_fetch_array($result);
            //print_r($row);
            $knownIds[$byuid] = $row["ID"];
            echo "Added BYUID $byuid into our known list of ids\n";
       } else {
            echo "Missing husband for row.";
            print_r ($data);
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
       } else {
            echo "Missing wife for row.  Adding her to the database";
            $arr = array ("Gender" => "Female");
            $id = "";

            $insert = get_insert_statement("Person", $arr);
            $res = pg_query($db_to, $insert);
            if ($res) {
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

            }
            print_r ($data);
            $currentID = md5($data["First Name"] . $data["Middle Name"] . $data["Last Name"]);
            $knownIds[$currentID] = $id;
            $data["Person Sealed to Household Male ID"] = $currentID;
            echo "Successfully added new woman to database";
       }

       $marriage["WifeID"] = $knownIds[$data["Person Sealed to Household Male ID"]];

       // Look up the wife id in the UVA database
       if ($data["Marriage Officiator"] != '' && !isset($knownIds[$data["Marriage Officiator"]])) { // look up wife ID
            $byuid = $data["Marriage Officiator"];
            $result = pg_query($db_to, "SELECT * FROM public.\"Person\" WHERE \"BYUID\"={$byuid} ORDER BY \"ID\" asc");
            if (!$result) {
                echo "An error occurred.\n";
                exit;
            }

            $row = pg_fetch_array($result);
            //print_r($row);
            $knownIds[$byuid] = $row["ID"];
            echo "Added BYUID $byuid into our known list of ids\n";
       }
       if ($data["Marriage Officiator"] != '')
           $marriage["OfficiatorID"] = $knownIds[$data["Marriage Officiator"]];
       
       // Look up the husband proxy id in the UVA database
       if ($data["MP Male"] != '' && !isset($knownIds[$data["MP Male"]])) { // look up husband id
            $byuid = $data["MP Male"];
            $result = pg_query($db_to, "SELECT * FROM public.\"Person\" WHERE \"BYUID\"={$byuid} ORDER BY \"ID\" asc");
            if (!$result) {
                echo "An error occurred.\n";
                exit;
            }

            $row = pg_fetch_array($result);
            //print_r($row);
            $knownIds[$byuid] = $row["ID"];
            echo "Added BYUID $byuid into our known list of ids\n";
       }
       if ($data["MP Male"] != '')
           $marriage["HusbandProxyID"] = $knownIds[$data["MP Male"]];

       // Look up the wife proxy id in the UVA database
       if ($data["MP Female"] != '' && !isset($knownIds[$data["MP Female"]])) { // look up husband id
            $byuid = $data["MP Female"];
            $result = pg_query($db_to, "SELECT * FROM public.\"Person\" WHERE \"BYUID\"={$byuid} ORDER BY \"ID\" asc");
            if (!$result) {
                echo "An error occurred.\n";
                exit;
            }

            $row = pg_fetch_array($result);
            //print_r($row);
            $knownIds[$byuid] = $row["ID"];
            echo "Added BYUID $byuid into our known list of ids\n";
       }
       if ($data["MP Female"] != '')
           $marriage["WifeProxyID"] = $knownIds[$data["MP Female"]];

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
       $marriage['Type'] = $types[$data["Marriage Type (civil, Time, Eternal)"]];

       // Set whether or not this is a root marriage
       if ($data["Root Wife"] == "Y")
               $marriage["Root"] = "true";
       else
               $marriage["Root"] = "false";

       // Set the marriage date
       if ($data["Marriage Date"] != '')
           $marriage['MarriageDate'] = $data["Marriage Date"];

       // Set the Divorce Date
       if ($data["Divorce Date"] != '')
           $marriage['DivorceDate'] = $data["Divorce Date"];


       // Update the marriage, if it exists

       // If marriage doesn't exist, then insert the marriage itself (with root boolean)
       // Then, insert husband, wife, proxies (if exist), officiator (if exist) into the personmarriage table


       // Grab the next line of the file
       $data1 = fgetcsv($csvfile);
}

fclose($csvfile);

function get_insert_statement($tableName, $arr) {
    $insert = "INSERT INTO public.\"$tableName\" ";
    $cols = "";
    $vals = "";
    foreach ($arr as $k => $v) {
            $cols .= "\"$k\",";
            if ($v == "") $v = "NULL";
            if ($k == "BYUID" || $k == "BYUChildOf" || $v == "NULL")
                $vals .= "$v,";
            else
                $vals .= "'$v',";
    }
    $cols = substr($cols, 0, -1);
    $vals = substr($vals, 0, -1);

    $insert .= "($cols) VALUES ($vals);";

    return $insert;
}
?>
