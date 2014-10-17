<?php
include_once("common_functions.php");

$DEBUG = true;
$db_to = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_data user=nauvoo password=p7qNpqygYU");

$csvfile = fopen("adoptions.csv", "r");
$badfile = fopen("failed_adoptions.csv", "w");
$newidfile = fopen("good_adoptions.csv", "w");
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
while ($data1 !== false) {
        $data = array();
        foreach ($data1 as $k => $v) 
                $data[$head[$k]] = trim($v);
        echo "Read one line: {$data["ID"]} : {$data["Last Name"]}, {$data["First Name"]}\n";

        // Full data is now available in data by text indexing

        // Ignore the row if it is specified to be skipped
        if (isset($data["Skip"]) && $data["Skip"] == "Y") {
                echo "       Skipped this row.\n";
                // Grab the next line of the file
                write_out_line($badfile, $data1);
                $data1 = fgetcsv($csvfile);
                continue;
        }
        // Ignore the row if the ID is empty
        if (!isset($data["ID"]) || $data["ID"] == "" || !is_numeric($data["ID"])) {
                echo "       Ignoring Empty or Illformed ID\n";
                // Grab the next line of the file
                write_out_line($badfile, $data1);
                $data1 = fgetcsv($csvfile);
                continue;
        }
        // Ignore the row if the Marriage ID is empty
        if (!isset($data["Marriage ID"]) || $data["Marriage ID"] == "") {
                echo "       Ignoring, not adopted to anyone\n";
                // Grab the next line of the file
                write_out_line($badfile, $data1);
                $data1 = fgetcsv($csvfile);
                continue;
        }

        // Put the information into its own structure
        $adoption = array();

        if (isset($data["Relation Family/Non-Family"]) && $data["Relation Family/Non-Family"] != "") {
            // this is a special type of relation
            $type = $data["Relation Family/Non-Family"];
            if ($type == "Son" || $type == "Daughter") {
                // should probably do something here, like is this natural type?
                //echo "       natural child?";
            }
        }
        $adoption["Type"] = "adoption";
        $adoption["AdopteeID"] = $data["ID"];
        $adoption["MarriageID"] = $data["Marriage ID"];
        if (isset($data["Place ID"]) &&$data["Place ID"] != "")
            $adoption["PlaceID"] = $data["Place ID"];
        if (isset($data["Officiator ID"]) &&$data["Officiator ID"] != "")
            $adoption["OfficiatorID"] = $data["Officiator ID"];

        // Look up the husband and wife proxy ids to get the proxy marriage id
        $tmp = "";
        if ($data["Marriage Proxies ID (Male)"] != '' && $data["Marriage Proxies ID (Female)"] != '') { // look up marriage proxy
                $result = pg_query($db_to, "SELECT m.\"ID\",m.\"Type\" FROM public.\"PersonMarriage\" pm1, public.\"PersonMarriage\" pm2, public.\"Marriage\" m WHERE pm1.\"PersonID\"={$data["Marriage Proxies ID (Male)"]} AND pm1.\"Role\"='Husband' AND pm2.\"PersonID\"={$data["Marriage Proxies ID (Female)"]} AND pm2.\"Role\"='Wife' AND pm1.\"MarriageID\" = pm2.\"MarriageID\" AND pm1.\"MarriageID\" = m.\"ID\" ORDER BY m.\"ID\" asc");
                if (!$result) {
                        echo "An error occurred.\n";
                        exit;
                }
                $ids = array();
                while ($row = pg_fetch_array($result)) {
                    $ids[$row['Type']] = $row['ID'];
                }
                //print_r($row);
                if (isset($ids['eternity'])) {
                    $adoption["MarriageProxyID"] = $ids['eternity'];
                } else if (isset($ids['time'])) {
                    $adoption["MarriageProxyID"] = $ids['time'];
                } else if (isset($ids['civil'])) {
                    $adoption["MarriageProxyID"] = $ids['civil'];
                } else {
                    echo "       Proxy exists, but could not find match\n";
                    // Grab the next line of the file
                    write_out_line($badfile, $data1);
                    $data1 = fgetcsv($csvfile);
                    continue;
                }
        }

        // Set the adoption date
        if ($data["Adoption Date"] != '' && preg_match("/^[0-9]{4}-?[0-9]{0,2}-?[0-9]{0,2}$/", $data["Adoption Date"]))
                $adoption['Date'] = $data["Adoption Date"];

        // Set the notes field
        $adoption["PrivateNotes"] = pg_escape_string($data["Notes"]);

        $insert = get_insert_statement("NonMaritalSealings", $adoption);
        if (!$DEBUG) {
                $result = pg_query($db_to, $insert);
                if (!$result) die("Error inserting adoption");
        } else { echo "$insert\n";}


        write_out_line($newidfile, $data1);
        // Grab the next line of the file
        $data1 = fgetcsv($csvfile);
}

fclose($csvfile);
fclose($badfile);
fclose($newidfile);

function write_out_line($file, $line) {
    fputcsv($file, $line);
}

?>
