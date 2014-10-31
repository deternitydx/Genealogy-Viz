<?php

header('Content-type: application/json');

$db = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_data user=nauvoo password=p7qNpqygYU");

$type = $_GET["type"];
$search = implode(" & ", explode(" ", pg_escape_string($_GET["q"])));

$query = "";

if ($type == "name") {
    $query = "SELECT DISTINCT p.\"ID\", n.\"First\", n.\"Middle\", n.\"Last\", p.\"Gender\", p.\"BirthDate\", p.\"DeathDate\", p.\"PrivateNotes\", p.\"PublicNotes\" FROM \"public\".\"Name\" n, \"public\".\"Person\" p WHERE n.\"First\" || ' ' || n.\"Last\" @@ to_tsquery('$search') AND n.\"Type\"='authoritative' AND n.\"PersonID\" = p.\"ID\" ORDER BY n.\"Last\", n.\"First\" ASC";
} else if ($type == "children") {
    $query = "SELECT DISTINCT p.\"ID\", n.\"First\", n.\"Middle\", n.\"Last\", p.\"Gender\", p.\"BirthDate\", p.\"DeathDate\", p.\"PrivateNotes\", p.\"PublicNotes\" FROM \"public\".\"Name\" n, \"public\".\"Person\" p, \"public\".\"PersonMarriage\" m WHERE m.\"PersonID\" = $search AND (m.\"Role\" = 'Husband' OR m.\"Role\" = 'Wife') AND n.\"Type\"='authoritative' AND n.\"PersonID\" = p.\"ID\" AND p.\"BiologicalChildOfMarriage\" = m.\"MarriageID\" ORDER BY n.\"Last\", n.\"First\", n.\"Middle\" ASC";
}

$result = pg_query($db, $query);
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