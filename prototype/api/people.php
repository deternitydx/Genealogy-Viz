<?php

header('Content-type: application/json');

$db = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_data user=nauvoo password=p7qNpqygYU");

$result = pg_query($db, "SELECT p.\"ID\",n.\"First\",n.\"Middle\",n.\"Last\",p.\"BirthDate\",p.\"DeathDate\",
       p.\"Gender\", p.\"BirthPlaceID\" FROM public.\"Person\" p, public.\"Name\" n WHERE p.\"ID\"=n.\"PersonID\" AND n.\"Type\"='authoritative'ORDER BY n.\"Last\", n.\"First\",n.\"Middle\" asc");
if (!$result) {
    exit;
}

$arr = pg_fetch_all($result);
echo "{ \n\"data\": [\n";
$json = array();
$first = true;
foreach ($arr as $mar) {
        $resa = array();
        $firsta = array();
    foreach ($mar as $k=>$v) {
            $clean = htmlspecialchars($v);
            if ($clean == "") $clean = "&nbsp;";
        array_push($resa, "\"$clean\"");
        if ($first) array_push($firsta, "\"$k\"");
	}
	
	
	array_push($json, "[" . implode(", ", $resa) . "]");
	$first = false;


}
	echo implode(",\n", $json);

echo "]\n }";

?>
