<?php
    header('Content-type: application/json');

    /*
     * If there is no query, then we will return a default list
     * that is helpful to UVA.
     */

    if (!isset($_GET['q']) || strlen($_GET['q']) < 1) {
        $people = array(
            array("id"=>25079, "text"=>"AML Amasa M. Lyman (1813-03-30 - 1877-02-04) 25079"),
            array("id"=>615, "text"=>"BY Brigham Young (1801-06-01 - 1877-08-29) 615"),
            //array("id"=>, "text"=>"CCR Charles C. Rich"),
            //array("id"=>, "text"=>"DS Daniel Spencer"),
            //array("id"=>, "text"=>"ETB Ezra Taft Benson"),
            array("id"=>484, "text"=>"GAS George A. Smith (1817-06-26 - 1875-09-01) 484"),
            array("id"=>15277, "text"=>"GM George Miller (1794-11-25 - 1856-01-01) 15277"),
            array("id"=>5720, "text"=>"HCK Heber C. Kimball (1801-06-14 - 1868-06-22) 5720"),
            array("id"=>15728, "text"=>"IM Isaac Morley (1786-03-11 - 1864-07-21) 15728"),
            array("id"=>32267, "text"=>"JT John Taylor (1808-11-01 - 1887-07-25) 32267"),
            array("id"=>31692, "text"=>"OH Orson Hyde (1805-01-08 - 1878-11-28) 31692"),
            array("id"=>425, "text"=>"OP Orson Pratt (1811-09-19 - 1881-10-03) 425"),
            array("id"=>7727, "text"=>"OS Orson Spencer (1802-03-14 - 1855-10-15) 7727"),
            array("id"=>428, "text"=>"PPP Parley P. Pratt (1807-04-12 - 1857-05-13) 428"),
            //array("id"=>, "text"=>"WdS Willard Snow"),
            //array("id"=>, "text"=>"WF Winslow Farr"),
            //array("id"=>, "text"=>"WH William Huntington"),
            //array("id"=>, "text"=>"WmS William Snow"),
            array("id"=>34674, "text"=>"WWP William W. Phelps (1792-02-17 - 1872-03-06) 34674")
            //array("id"=>, "text"=>"ZC Zebedee Coltrin")
        );

        echo json_encode($people);
        exit();
    }
    $q = $_GET['q'];

    $db = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_data_test user=nauvoo password=p7qNpqygYU");

    $query = "
        SELECT DISTINCT p.*, n.\"First\", n.\"Last\", n.\"Type\"

        FROM public.\"Name\" n

        LEFT JOIN public.\"Person\" p ON p.\"ID\" = n.\"PersonID\" 
        
        WHERE 
        n.\"First\" || ' ' || n.\"Last\" ilike '%$q%'

        ORDER BY n.\"Last\", n.\"First\" ASC";
    $result = pg_query($db, $query);
    if (!$result) {
        exit;
    }
    $results = pg_fetch_all($result);

    $people = array();

    foreach($results as $res) {
        array_push($people, array("id"=>$res["ID"], "text"=> $res["Last"] . ", " . $res["First"] . " (" . $res["BirthDate"] . " -- " . $res["DeathDate"] . ")"));
    }
    echo json_encode($people);
?>
