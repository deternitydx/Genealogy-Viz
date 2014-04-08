
<?php

//header('Content-type: application/json');

$col = "DeathDateText";
$wri = "DeathDate";
$pre = "DeathDateSearchable";

$tab = "Person";
$idc = "ID"; 

$db = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_new user=nauvoo password=p7qNpqygYU");

$result = pg_query($db, "SELECT * FROM public.\"$tab\"");
if (!$result) {
    echo "An error occurred.\n";
    exit;
}

$arr = pg_fetch_all($result);

foreach ($arr as $entry) {

	$ID = $entry[$idc];

	$tmp = $entry[$col];
	$out = "";
	if ($entry[$pre] != "")	// already processed, then good
		$out = $entry[$pre];
	else {
		if (strlen($tmp) == 4)
			$tmp = $tmp . "-01-01";
		
		if ($tmp == "")
			$out = "";
		else
			$out = date_format(date_create($tmp), "Y-m-d");
	}

	$res = pg_query($db, "UPDATE public.\"$tab\" SET \"$wri\" = '$out' WHERE \"$idc\" = $ID");
	if (!$result)
		die ("Something went wrong.");
	echo "Updated record: $ID to the following: $tmp => $out<br>"; 
}


?>
