<?php
    $id = null;
    // Get the person ID
    if (isset($_GET["id"]) && is_numeric($_GET["id"]))
        $id = $_GET["id"];
    else
        die("Please provide a numeric id");
    
    header('Content-type: application/json');
    
    $db = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_data user=nauvoo password=p7qNpqygYU");
    
    // Array to hold all information about the person
    $person = array();

    // Get Personal Information
    $result = pg_query($db, "SELECT * FROM public.\"Person\" p
    WHERE p.\"ID\"=$id");
    if (!$result) {
        exit;
    }
    foreach(pg_fetch_all($result) as $res) {
        $person["information"] = $res;
    }

    // Get All Names
    $result = pg_query($db, "SELECT * FROM public.\"Name\" n WHERE n.\"PersonID\"=$id");
    if (!$result) {
        exit;
    }
    $person["names"] = pg_fetch_all($result);

    // Get Non-Marital Sealings
    $result = pg_query($db, "SELECT * FROM public.\"NonMaritalSealings\" n WHERE n.\"AdopteeID\"=$id");
    if (!$result) {
        exit;
    }
    $person["non_marital_sealings"] = pg_fetch_all($result);

    // Get Temple Rites
    $result = pg_query($db, "SELECT * FROM public.\"NonMaritalTempleRites\" n WHERE n.\"PersonID\"=$id");
    if (!$result) {
        exit;
    }
    $person["temple_rites"] = pg_fetch_all($result);


    // Get All Marriages
    $result = null;
    if ($person["information"]["Gender"] == "Male") 
        $result = pg_query($db, "SELECT m.\"ID\", m.\"PlaceID\", m.\"MarriageDate\", m.\"DivorceDate\",
                                    m.\"CancelledDate\", m.\"Type\", w.\"PersonID\" as \"WifeID\", 
                                    h.\"PersonID\" as \"HusbandID\", m.\"Root\" FROM public.\"Marriage\" m, 
                                    public.\"PersonMarriage\" h, public.\"PersonMarriage\" w WHERE
                                    h.\"MarriageID\" = m.\"ID\" AND h.\"Role\" = 'Husband' 
                                    AND w.\"MarriageID\" = m.\"ID\" AND w.\"Role\" = 'Wife' 
                                    AND h.\"PersonID\"=$id ORDER BY m.\"MarriageDate\" ASC");
    else
        $result = pg_query($db, "SELECT m.\"ID\", m.\"PlaceID\", m.\"MarriageDate\", m.\"DivorceDate\",
                                    m.\"CancelledDate\", m.\"Type\", w.\"PersonID\" as \"WifeID\", 
                                    h.\"PersonID\" as \"HusbandID\", m.\"Root\" FROM public.\"Marriage\" m, 
                                    public.\"PersonMarriage\" h, public.\"PersonMarriage\" w WHERE
                                    h.\"MarriageID\" = m.\"ID\" AND h.\"Role\" = 'Husband' 
                                    AND w.\"MarriageID\" = m.\"ID\" AND w.\"Role\" = 'Wife' 
                                    AND w.\"PersonID\"=$id ORDER BY m.\"MarriageDate\" ASC");
    
    if (!$result) {
        print_empty("Error finding marriages.");
        exit;
    }

    $person["marriages"] = pg_fetch_all($result);
    // Return the person array as json to be used by the editor:
    echo json_encode($person);
?>
