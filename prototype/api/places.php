<?php

header('Content-type: application/json');

$db = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_data_test user=nauvoo password=p7qNpqygYU");

$result = pg_query($db, "SELECT p.\"ID\",p.\"DisplayName\",p.\"OfficialName\" FROM public.\"Place\" p ORDER BY p.\"OfficialName\", p.\"ID\" asc");
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
            if ($v == "") $v = "&nbsp;";
        array_push($resa, json_encode($v));
        if ($first) array_push($firsta, "\"$k\"");
	}
	
	
	array_push($json, "[" . implode(", ", $resa) . "]");
	$first = false;


}
	echo implode(",\n", $json);

echo "]\n }";

?>
