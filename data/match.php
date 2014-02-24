<?php

	// Read Census File
	$census_data = file("csv/1852census.csv", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	$census_names = array();
	foreach ($census_data as $i => $line) {
			$split = split(",", strtolower($line));
			if ($split[5] != "last" && $split[6] != "first")
				$census_names[$i+1] = $split[5] . ", " . $split[6];
	}
	unset($census_data);

	// Read Land Patents File
	$land_data = file("csv/landpatents.csv", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	$land_names = array();
	foreach($land_data as $line) {
		$split = split(",", $line);
		$land_names[$split[0]] = strtolower($split[1] . ", " . $split[2]);
	}
	unset($land_data);

	// Read Tax File
	$tax_data = file("csv/taxrecords2.csv", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	$tax_names = array();
	foreach($tax_data as $i => $line) {
		$split = str_getcsv($line);  // defaults to , with "
		if ($split[0] != "" && isset($split[1]) && $split[1] == "" && isset($split[2]) && $split[2] == "" && isset($split[6]) && $split[6] == "") {
			$name = $split[0];
			$split2 = split(",", $name);
			$lname = $split2[0];
			$split3 = array("");
			if (isset($split2[1]))
				$split3 = split(" ", trim($split2[1]));
			$fname = $split3[0];
			//print_r(split(",", $name));
			//list($lname, $junk) = split(",", $name);
			//list($fname, $junk) = split(" ", $junk);

			$tax_names[$i + 1] = strtolower($lname . ", " . $fname);
		}	
	}	

	unset($tax_data);

//	print_r($tax_names);








	$unique_names = array_unique($tax_names);




//	print_r($census_names);
//	print_r($land_names);

	$output = "Name,Tax Record Row(s),1852 Census Row(s),Land Patent ID(s)\n";

	foreach ($unique_names as $i => $name) {
		$lids = array();
		$cids = array();
		$tids = array();
		foreach($tax_names as $id => $tname) {
			if ($name == $tname) {
				array_push($tids, $id);
			}
		}
		foreach($census_names as $id => $cname) {
			if ($name == $cname) {
				array_push($cids, $id);
			}
		}
		foreach($land_names as $id => $lname) {
			if ($name == $lname) {
				array_push($lids, $id);
			}
		}
		if (count($lids) > 0 || count($cids) > 0) { // there is at least one element
			$output .= "\"$name\"," .implode(";", $tids) .",". implode(";",$cids) . "," . implode(";", $lids) . "\n";
		}
	}

	echo $output;

?>
