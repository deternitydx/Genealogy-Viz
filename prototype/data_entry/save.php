<?php
    include_once("common_functions.php");
    $sealings = array();
    $marriages = array();
    $rites = array();
    $personal = array();
    $names = array();

    // Break apart the POST values into their respective parts
    foreach ($_POST as $key => $val) {
        $pieces = explode("_", $key);
        $i = end($pieces);
        switch ($pieces[0]) {
            case "name":
                if (!isset($names[$i]))
                    $names[$i] = array();
                unset($pieces[0]);
                unset($pieces[count($pieces)]);
                $names[$i][implode("_", $pieces)] = $val;
                break;
            case "mar":
                if (!isset($marriages[$i]))
                    $marriages[$i] = array();
                unset($pieces[0]);
                unset($pieces[count($pieces)]);
                $marriages[$i][implode("_", $pieces)] = $val;
                break;
            case "tr":
                if (!isset($rites[$i]))
                    $rites[$i] = array();
                unset($pieces[0]);
                unset($pieces[count($pieces)]);
                $rites[$i][implode("_", $pieces)] = $val;
                break;
            case "nms":
                if (!isset($sealings[$i]))
                    $sealings[$i] = array();
                unset($pieces[0]);
                unset($pieces[count($pieces)]);
                $sealings[$i][implode("_", $pieces)] = $val;
                break;
            default:
                $personal[$key] = $val;
        }
    }
    
    // Logging
    //
    $output = fopen("submissions.txt", "a+");
    fwrite($output, "=======================================\n");
    fwrite($output, date(DATE_RFC2822, time()) . "\n");
    fwrite($output, "=======================================\n");
    //fwrite($output, print_r($_POST, true));

    //fwrite($output, "=======================================\n");

    //fwrite($output, print_r($personal, true));
    //fwrite($output, print_r($names, true));
    //fwrite($output, print_r($marriages, true));
    //fwrite($output, print_r($sealings, true));
    //fwrite($output, print_r($rites, true));

    // Some constants (marital roles)
    $mrole = "Wife";
    $srole = "Husband";
    if ($personal["gender"] == "Male") {
        $mrole = "Husband";
        $srole = "Wife";
    }

    // Handle each part of the submit to insert into the database
    foreach ($marriages as $marriage) {
        $vals = array();
        /**
            [1] => Array
                (
                    [id] => 353
                    [type] => civil
                    [spouse_person_id] => 617
                    [date_month] => 03
                    [date_day] => 07
                    [date_year] => 1841
                    [div_month] => MM
                    [div_day] => DD
                    [div_year] => YYYY
                    [cancel_month] => MM
                    [cancel_day] => DD
                    [cancel_year] => YYYY
                    [place_id] => 73
                    [officiator_person_id] => 
                    [proxy_person_id] => 
                    [spouse_proxy_person_id] => 
                    [name_id] => 
                )
                **/

        // Marriage table first
        if (isset($marriage["type"]))
            $vals["Type"] = $marriage["type"];
        else
            $vals["Type"] = "unknown";
        if (isset($marriage["place_id"]))
            $vals["PlaceID"] = $marriage["place_id"];
        if (isset($marriage["date_year"]) && isset($marriage["date_month"]) && isset($marriage["date_day"]))
            $vals["MarriageDate"] = combine_date($marriage["date_year"] , $marriage["date_month"], $marriage["date_day"]);
        if (isset($marriage["div_year"]) && isset($marriage["div_month"]) && isset($marriage["div_day"]))
            $vals["DivorceDate"] = combine_date($marriage["div_year"] , $marriage["div_month"], $marriage["div_day"]);
        if (isset($marriage["cancel_year"]) && isset($marriage["cancel_month"]) && isset($marriage["cancel_day"]))
            $vals["CancelledDate"] = combine_date($marriage["cancel_year"] , $marriage["cancel_month"], $marriage["cancel_day"]);
        if ($marriage["id"] == "NEW")
            $marriage["id"] = insert("Marriage", $vals);
        else
            update("Marriage", $vals, "\"ID\" = " . $marriage["id"]);

        // Handle each of the Participants
        // This person
        $vals = array();
        $vals["MarriageID"] = $marriage["id"];
        $vals["PersonID"] = $personal["ID"];
        $vals["Role"] = $mrole;
        if (!update("PersonMarriage", $vals, "\"MarriageID\" = " . $vals["MarriageID"] . 
            " AND \"Role\" = \"" . $vals["Role"] . "\""))
            insert("PersonMarriage", $vals);
        // Spouse
        if (isset($marriage["spouse_person_id"]) && $marriage["spouse_person_id"] != "") {
            $vals = array();
            $vals["MarriageID"] = $marriage["id"];
            $vals["PersonID"] = $marriage["spouse_person_id"];
            $vals["Role"] = $srole;
            if (!update("PersonMarriage", $vals, "\"MarriageID\" = " . $vals["MarriageID"] . 
                " AND \"Role\" = \"" . $vals["Role"] . "\""))
                insert("PersonMarriage", $vals);
        }
        // Proxy
        if (isset($marriage["proxy_person_id"]) && $marriage["proxy_person_id"] != "") {
            $vals = array();
            $vals["MarriageID"] = $marriage["id"];
            $vals["PersonID"] = $marriage["proxy_person_id"];
            $vals["Role"] = "Proxy".$mrole;
            if (!update("PersonMarriage", $vals, "\"MarriageID\" = " . $vals["MarriageID"] . 
                " AND \"Role\" = \"" . $vals["Role"] . "\""))
                insert("PersonMarriage", $vals);
        }
        // Spouse Proxy
        if (isset($marriage["spouse_proxy_person_id"]) && $marriage["spouse_proxy_person_id"] != "") {
            $vals = array();
            $vals["MarriageID"] = $marriage["id"];
            $vals["PersonID"] = $marriage["spouse_proxy_person_id"];
            $vals["Role"] = "Proxy".$srole;
            if (!update("PersonMarriage", $vals, "\"MarriageID\" = " . $vals["MarriageID"] . 
                " AND \"Role\" = \"" . $vals["Role"] . "\""))
                insert("PersonMarriage", $vals);
        }
        // Officiator
        if (isset($marriage["officiator_person_id"]) && $marriage["officiator_person_id"] != "") {
            $vals = array();
            $vals["MarriageID"] = $marriage["id"];
            $vals["PersonID"] = $marriage["officiator_person_id"];
            $vals["Role"] = "Officiator";
            if (!update("PersonMarriage", $vals, "\"MarriageID\" = " . $vals["MarriageID"] . 
                " AND \"Role\" = \"" . $vals["Role"] . "\""))
                insert("PersonMarriage", $vals);
        }
    }

    foreach ($names as $name) {
        $vals = array();
        /**
            [1] => Array
                (
                    [id] => 52338
                    [type] => authoritative
                    [prefix] => 
                    [first] => Zina
                    [middle] => Diantha
                    [last] => Huntington
                    [suffix] => 
                )
                **/
        if (isset($name["type"]))
            $vals["Type"] = $name["type"];
        if (isset($name["prefix"]))
            $vals["Prefix"] = $name["prefix"];
        if (isset($name["first"]))
            $vals["First"] = $name["first"];
        if (isset($name["middle"]))
            $vals["Middle"] = $name["middle"];
        if (isset($name["last"]))
            $vals["Last"] = $name["last"];
        if (isset($name["suffix"]))
            $vals["Suffix"] = $name["suffix"];

        // Add the person id from the main page
        $vals["PersonID"] = $personal["ID"];

        if ($name["id"] == "NEW")
            // do insert
            insert("Name", $vals);
        else {
            // do update
            update("Name", $vals, "\"ID\" = " . $name["id"]);
        }
    }

    foreach ($rites as $rite) {
        $vals = array();
        /**
            [1] => Array
                (
                    [id] => NEW
                    [type] => endowment
                    [date_month] => MM
                    [date_day] => DD
                    [date_year] => YYYY
                    [place_id] => 7882
                    [officiator_person_id] => 4194
                    [officiator_role] => asdf
                    [proxy_person_id] => 55173
                    [annointed_to_person_id] => 9422
                    [annointed_to_proxy_person_id] => 4194
                    [name_id] => 
                )
                **/
        $vals["PersonID"] = $personal["ID"];
        if (isset($rite["type"]))
            $vals["Type"] = $rite["type"];
        if (isset($rite["proxy_person_id"]))
            $vals["ProxyID"] = $rite["proxy_person_id"];
        if (isset($rite["annointed_to_person_id"]))
            $vals["AnnointedToID"] = $rite["annointed_to_person_id"];
        if (isset($rite["annointed_to_proxy_person_id"]))
            $vals["AnnointedToProxyID"] = $rite["annointed_to_proxy_person_id"];
        if (isset($rite["place_id"]))
            $vals["PlaceID"] = $rite["place_id"];
        if (isset($rite["date_year"]) && isset($rite["date_month"]) && isset($rite["date_day"]))
            $vals["Date"] = combine_date($rite["date_year"], $rite["date_month"], $rite["date_day"]);

        if ($rite["id"] == "NEW")
            $rite["id"] = insert("NonMaritalTempleRites", $vals);
        else {
            update("NonMaritalTempleRites", $vals, "\"ID\" = " . $rite["id"]);
        }

        // Add the officiator, if not already set:
        $vals = array();
        if (isset($rite["officiator_person_id"]))
            $vals["PersonID"] = $rite["officiator_person_id"];
        if (isset($rite["officiator_role"]))
            $vals["Role"] = $rite["officiator_role"];
        $vals["NonMaritalTempleRitesID"] = $rite["id"];
        if (!update("TempleRiteOfficiators", $vals, "\"NonMaritalTempleRitesID\" = " . $vals["NonMaritalTempleRitesID"]
            . " AND \"PersonID\" = " . $vals["PersonID"]))
            insert("TempleRiteOfficiators", $vals);

    }

    foreach ($sealings as $sealing) {
        $vals = array();
        /**
            [1] => Array
                (
                    [id] => NEW
                    [type] => adoption
                    [date_month] => MM
                    [date_day] => DD
                    [date_year] => YYYY
                    [place_id] => 16976
                    [officiator_person_id] => 4194
                    [proxy_person_id] => 31062
                    [marriage_id] => 13016
                    [proxy_marriage_id] => 914
                    [name_id] => 
                )
                **/

        $vals["AdopteeID"] = $personal["ID"];
        if (isset($sealing["type"]))
            $vals["Type"] = $sealing["type"];
        if (isset($sealing["person_proxy_id"]))
            $vals["AdopteeProxyID"] = $sealing["proxy_person_id"];
        if (isset($sealing["marriage_id"]))
            $vals["MarriageID"] = $sealing["marriage_id"];
        if (isset($sealing["proxy_marriage_id"]))
            $vals["MarriageProxyID"] = $sealing["proxy_marriage_id"];
        if (isset($sealing["officiator_person_id"]))
            $vals["OfficiatorID"] = $sealing["officiator_person_id"];
        if (isset($sealing["place_id"]))
            $vals["PlaceID"] = $sealing["place_id"];

        // need to add Name as Sealed to the DB
        // $vals["NameID"] = $sealing["name_id"];

        if (isset($sealing["date_year"]) && isset($sealing["date_year"]) && isset($sealing["date_year"]))
            $date = combine_date($sealing["date_year"], $sealing["date_month"], $sealing["date_day"]);;

        if ($sealing["id"] == "NEW")
            // do insert
            insert("NonMaritalSealings", $vals);
        else {
            // do update
            update("NonMaritalSealings", $vals, "\"ID\" = " . $sealing["id"]);
        }
    }

    // Insert all the personal data back in

    /**
        Array
        (
            [ID] => 1907
            [BrownID] => 2971
            [birthmonth] => 01
            [birthday] => 31
            [birthyear] => 1821
            [b_place_id] => 262
            [b_marriage_id] => 156
            [deathmonth] => 08
            [deathday] => 27
            [deathyear] => 1901
            [d_place_id] => 37
            [n_i] => 8
            [r_i] => 1
            [s_i] => 1
            [m_i] => 6
        )
     **/

    $vals = array();
    if(isset($personal["gender"]))
        $vals["Gender"] = $personal["gender"];
    if(isset($personal["b_marriage_id"]))
        $vals["BiologicalChildOfMarriage"] = $personal["b_marriage_id"];
    if(isset($personal["b_place_id"]))
        $vals["BirthPlaceID"] = $personal["b_place_id"];
    if(isset($personal["d_place_id"]))
        $vals["DeathPlaceID"] = $personal["d_place_id"];
    if(isset($personal["birthyear"]) && isset($personal["birthmonth"]) && isset($personal["birthday"]))
        $vals["BirthDate"] = combine_date($personal["birthyear"], $personal["birthmonth"], $personal["birthday"]);
    if(isset($personal["deathyear"]) && isset($personal["deathmonth"]) && isset($personal["deathday"]))
        $vals["DeathDate"] = combine_date($personal["deathyear"], $personal["deathmonth"], $personal["deathday"]);
    
    if ($personal["ID"] == "NEW")
        // do insert
        insert("Person", $vals);
    else {
        // do update
        update("Person", $vals, "\"ID\" = " . $personal["ID"]);
    }

    // Output the results as a paper trail, just in case something bad happens

    fwrite($output, "\n\n");
    fclose($output);

    echo "success";
?>
