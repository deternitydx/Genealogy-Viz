<?php
header('Content-type: application/json');

$where = "";
if (isset($_GET["parentsID"])) {
    $where = "AND p.\"BiologicalChildOfMarriage\"=" . $_GET["parentsID"];
}

$db = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_data user=nauvoo password=p7qNpqygYU");

$result = pg_query($db, "SELECT DISTINCT n.\"First\", n.\"Middle\", n.\"Last\", p.\"Gender\", p.\"BirthDate\", p.\"DeathDate\", p.\"ID\"  FROM \"public\".\"Name\" n, \"public\".\"Person\" p WHERE n.\"Type\"='authoritative' AND n.\"PersonID\" = p.\"ID\" ORDER BY n.\"Last\", n.\"First\" ASC");



/** SELECT n.\"First\"||' '||n.\"Middle\"||' '||n.\"Last\" as \"Name\",p.\"BirthDate\",p.\"DeathDate\",
    p.\"Gender\", p.\"ID\" FROM public.\"Person\" p, public.\"Name\" n
    WHERE p.\"ID\"=n.\"PersonID\" AND n.\"Type\"='authoritative' $where
    ORDER BY n.\"Last\", n.\"First\",n.\"Middle\" asc");
    **/
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
        if ($k=="ID") {
            $clean = "<a href='individual.php?id=$clean'>Edit</a>";    
        }
        array_push($resa, "\"$clean\"");
        if ($first) array_push($firsta, "\"$k\"");
    }


    array_push($json, "[" . implode(", ", $resa) . "]");
    $first = false;


}
echo implode(",\n", $json);

echo "]\n }";

?>
