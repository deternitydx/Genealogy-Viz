<?php

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

        if ($marriage["id"] == "NEW")
            // do insert
            continue;
        else
            // do update
            continue;
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
        if ($name["id"] == "NEW")
            // do insert
            continue;
        else
            // do update
            continue;
    }

    foreach ($rites as $rite) {
        $vals = array();
        /**
            [1] => Array
                (
                    [id] => NEW
                    [type] => adoption
                    [date_month] => MM 
                    [date_day] => DD
                    [date_year] => YYYY
                    [officiator_person_id] => 615
                    [name_id] => 
                )
         **/
        if ($rite["id"] == "NEW")
            // do insert
            continue;
        else
            // do update
            continue;
    }

    foreach ($sealings as $sealing) {
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
                    [officiator_person_id] => 
                    [officiator_role] => Test
                    [proxy_person_id] => 25212
                    [name_id] => 
                )
         **/
        if ($sealing["id"] == "NEW")
            // do insert
            continue;
        else
            // do update
            continue;
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

    // Output the results as a paper trail, just in case something bad happens
    $output = fopen("submissions.txt", "a+");
    fwrite($output, "=======================================\n");
    fwrite($output, date(DATE_RFC2822, time()) . "\n");
    fwrite($output, "=======================================\n");
    fwrite($output, print_r($_POST, true));

    fwrite($output, "=======================================\n");

    fwrite($output, print_r($personal, true));
    fwrite($output, print_r($names, true));
    fwrite($output, print_r($marriages, true));
    fwrite($output, print_r($sealings, true));
    fwrite($output, print_r($rites, true));

    fwrite($output, "\n\n");
    fclose($output);

    echo "success";
?>
