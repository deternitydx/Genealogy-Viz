<?php
    header('Content-type: application/json');

    if (!isset($_GET['q'])) {
        echo "{ 'error': 'no search term given'}";
        die();
    }

    $q = $_GET['q'];

    $db = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_data_test user=nauvoo password=p7qNpqygYU");

    $query = "
        SELECT DISTINCT m.*, pl.\"OfficialName\" as \"PlaceName\", hn.\"First\" as \"HusbandFirst\", hn.\"Last\" as \"HusbandLast\", wn.\"First\" as \"WifeFirst\", wn.\"Last\" as \"WifeLast\" 

        FROM public.\"Marriage\" m

        LEFT JOIN public.\"PersonMarriage\" hpm ON hpm.\"MarriageID\" = m.\"ID\" AND hpm.\"Role\" = 'Husband'
        LEFT JOIN public.\"PersonMarriage\" wpm ON wpm.\"MarriageID\" = m.\"ID\" AND wpm.\"Role\" = 'Wife'

        LEFT JOIN public.\"Name\" hn ON hpm.\"PersonID\" = hn.\"PersonID\" AND hn.\"Type\" = 'authoritative'
        LEFT JOIN public.\"Name\" wn ON wpm.\"PersonID\" = wn.\"PersonID\" AND wn.\"Type\" = 'authoritative' 

        LEFT JOIN public.\"Place\" pl ON m.\"PlaceID\" = pl.\"ID\"

        WHERE 
        hn.\"Last\" ilike '%$q%'
        OR hn.\"First\" ilike '%$q%'
        OR wn.\"Last\" ilike '%$q%'
        OR wn.\"First\" ilike '%$q%'

        ORDER BY hn.\"Last\", hn.\"First\", wn.\"Last\", wn.\"First\" ASC";
    // Need to select join personmarriage with name for the husbands and wives and marriage for the type
    $result = pg_query($db, $query);
    if (!$result) {
        exit;
    }
    $results = pg_fetch_all($result);

    $marriages = array();

    foreach($results as $res) {
        array_push($marriages, array("id"=>$res["ID"], "text"=> $res["HusbandLast"] . ", " . $res["HusbandFirst"] . " to " . $res["WifeLast"] . ", " . $res["WifeFirst"] . " (" . $res["MarriageDate"] . " : " . $res["Type"] . ")"));
    }
    echo json_encode($marriages);
?>
