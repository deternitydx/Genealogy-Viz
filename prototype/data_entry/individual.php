<!DOCTYPE html>
<!--
    Notes
    -----
    TODO: Need to add Marriage ID, Temple Rites ID, Name ID, and Sealing ID for any sealings pulled from the database
-->

<?php
    // load the person
    $person = json_decode(file_get_contents("http://ford.cs.virginia.edu/nauvoo/api/edit_person.php?id=".$_GET["id"]), true);
    // load the brown data
    $brown = json_decode(file_get_contents("http://ford.cs.virginia.edu/nauvoo/api/brown_individual.php?id=".$_GET["brown"]), true);
    $brown = $brown[0];

    $bdate = explode("-", $person["information"]["BirthDate"]);
    if (!isset($bdate[0]) || empty($bdate[0]))
        $bdate[0] = "YYYY";
    if (!isset($bdate[1]) || empty($bdate[1]))
        $bdate[1] = "MM";
    if (!isset($bdate[2]) || empty($bdate[2]))
        $bdate[2] = "DD";
    $ddate = explode("-", $person["information"]["DeathDate"]);
    if (!isset($ddate[0]) || empty($ddate[0]))
        $ddate[0] = "YYYY";
    if (!isset($ddate[1]) || empty($ddate[1]))
        $ddate[1] = "MM";
    if (!isset($ddate[2]) || empty($ddate[2]))
        $ddate[2] = "DD";
?>
<html>
    <head>
        <title>Nauvoo - Edit Entry</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="css/form.css" rel="stylesheet" media="screen">
        <link href="css/jquery.fancybox.css" rel="stylesheet" media="screen">
        <link rel="stylesheet" type="text/css" href="css/styles.css" media="all">
        <script type="text/javascript" src="js/jquery-1-10-2-min.js"></script>
        <script type="text/javascript" src="js/chosen.jquery.js"></script>
        <script type="text/javascript" src="js/bootstrap.min.js"></script>
        <!--<script type="text/javascript" src="js/custom-form.js"></script>
        <script type="text/javascript" src="js/custom-form.scrollable.js"></script>
        <script type="text/javascript" src="js/custom-form.file.js"></script>-->
        <script type="text/javascript" src="js/jquery.mousewheel-3.0.6.pack.js"></script>
        <script type="text/javascript" src="js/jquery.fancybox.pack.js"></script>
        <script type="text/javascript" src="js/scripts.js"></script>
        <link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0-rc.1/css/select2.min.css" rel="stylesheet" />
        <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0-rc.1/js/select2.min.js"></script>
    </head>
    <body>
        <div id="wrapper">
            <header>
            <div class="container">
                <strong class="logo"><a href="/">Nauvoo Database</a></strong>
            </div><!-- container -->
            </header><!-- header -->
            <div class="main-area container">
                <div class="page-header page-header-01">
                    <div class="frame">
                        <div class="clearfix">
                            <a href="index.php" class="back-link">Back to List</a>
                        </div>
                    </div><!-- frame -->
                    <h1>Edit Person</h1>
                </div><!-- page-header -->
                <div class="alert alert-01 alert-success" style="display: none">
                    <p>Successfully saved!</p>
                </div><!-- end alert -->
                <div class="alert alert-01 alert-failure" style="display: none">
                    <p>An error occured while saving</p>
                </div><!-- end alert -->
                <div class="clearfix">
                    <form id="nauvoo_form" action="#">
                        <fieldset>
                            <!-- This is the sidebar -->
                            <aside id="aside">
                                <h2 class="visible-md visible-lg">Record Information</h2>
                                <div class="box">
                                    <a href="#" data-toggle="modal" data-target="#add-new" class="add-new">Add New record</a>
                                </div><!-- box -->
                                <div class="box">
                                    <button id="button-record-save" class="btn btn-success btn-save ie-fix"><span>Save</span></button>
                                </div><!-- box -->
                                <div class="details-bar">
                                    <a href="#" data-toggle="dropdown" class="open-close"></a>
                                    <div class="drop dropdown-menu" role="menu">
                                        <div class="info-box">
                                            <dl>
                                            <dt class="visible-md visible-lg">UVA Person ID:</dt><dd class="visible-md visible-lg"><?=$person["information"]["ID"]?></dd>
                                            <dt class="visible-md visible-lg">Brown ID:</dt><dd class="visible-md visible-lg"><?=$_GET["brown"]?></dd>
                                            <input type="hidden" name="ID" id="ID" value="<?=$person["information"]["ID"]?>">
                                            <input type="hidden" name="BrownID" id="BrownID" value="<?=$_GET["brown"]?>">
                                            </dl>
                                        </div><!-- info-box -->
                                    </div>
                                </div><!-- details-bar -->
                                <h2 class="visible-md visible-lg">Brown Information</h2>
                                <div class="box">
                                    <h3>Context</h3>
                                    <div class="subbox">
                                        <p><?=$brown["context"]?></p>
                                    </div>
                                    <h3>Name</h3>
                                    <div class="subbox">
                                        <h4><?=$brown["Name"]?></h4>
                                        <p><?=$brown["NameFootnotes"]?></p>
                                    </div>
                                    <h3>Birthdate</h3>
                                    <div class="subbox">
                                        <h4><?=$brown["BD"]?></h4>
                                        <p><?=$brown["BDFootnotes"]?></p>
                                    </div>
                                    <h3>Priesthood</h3>
                                    <div class="subbox">
                                        <h4><?=$brown["PH"]?></h4>
                                        <p><?=$brown["PHFootnotes"]?></p>
                                    </div>
                                    <h3>Endowment</h3>
                                    <div class="subbox">
                                        <h4><?=$brown["E"]?></h4>
                                        <p><?=$brown["EFootnotes"]?></p>
                                    </div>
                                    <h3>Sealed / Marriage</h3>
                                    <div class="subbox">
                                        <h4><?=$brown["SM"]?></h4>
                                        <p><?=$brown["SMFootnotes"]?></p>
                                    </div>
                                    <h3>Adopted</h3>
                                    <div class="subbox">
                                        <h4><?=$brown["ASC"]?></h4>
                                        <p><?=$brown["ASCFootnotes"]?></p>
                                    </div>
                                    <h3>Second Annointing</h3>
                                    <div class="subbox">
                                        <h4><?=$brown["SA"]?></h4>
                                        <p><?=$brown["SAFootnotes"]?></p>
                                    </div>
                                </div>
                            </aside><!-- aside -->
                            <section class="tabs">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#tab-01" data-toggle="tab">Personal Information</a></li>
                                <li><a href="#tab-02" data-toggle="tab">Temple Rites</a></li>
                                <li><a href="#tab-03" data-toggle="tab">Non-Marital Sealings</a></li>
                                <li><a href="#tab-04" data-toggle="tab">Marital Sealings</a></li>
                            </ul><!-- nav-tabs -->
                            <div class="tab-content">
                                <!-- Personal Information -->
                                <div class="tab-pane active" id="tab-01">
                                    <section class="section">
                                    <div class="heading">
                                        <h2>Authoritative Name</h2>
                                    </div>
                                    <div class="form-area name-form">
<?php
    $n_i = 1;
    foreach ($person["names"] as $name) {
        if ($name["Type"] == 'authoritative') {
            echo "
                                        <div class=\"row-area\">
                                            <input type=\"hidden\" class=\"form-control\" value=\"{$name["ID"]}\" id=\"name_id_$n_i\" name=\"name_id_$n_i\">
                                            <input type=\"hidden\" class=\"form-control\" value=\"authoritative\" id=\"name_type_$n_i\" name=\"name_type_$n_i\">
                                            <div class=\"frame\">
                                                <input type=\"text\" class=\"form-control\" value=\"{$name["Prefix"]}\" id=\"name_prefix_$n_i\" name=\"name_prefix_$n_i\" size=\"4\">
                                                <label for=\"name_prefix_$n_i\">Prefix</label>
                                            </div>
                                            <div class=\"frame\">
                                                <input type=\"text\" class=\"form-control\" value=\"{$name["First"]}\" id=\"name_first_$n_i\" name=\"name_first_$n_i\" size=\"14\">
                                                <label for=\"name_first_$n_i\">First</label>
                                            </div>
                                            <div class=\"frame\">
                                                <input type=\"text\" class=\"form-control\" value=\"{$name["Middle"]}\" id=\"name_middle_$n_i\" name=\"name_middle_$n_i\" size=\"13\">
                                                <label for=\"name_middle_$n_i\">Middle</label>
                                            </div>
                                            <div class=\"frame\">
                                                <input type=\"text\" class=\"form-control\" value=\"{$name["Last"]}\" id=\"name_last_$n_i\" name=\"name_last_$n_i\" size=\"14\">
                                                <label for=\"name_last_$n_i\">Last</label>
                                            </div>
                                            <div class=\"frame\">
                                                <input type=\"text\" class=\"form-control\" value=\"{$name["Suffix"]}\" id=\"name_suffix_$n_i\" name=\"name_suffix_$n_i\" size=\"4\">
                                                <label for=\"name_suffix_$n_i\">Suffix</label>
                                            </div>
                                        </div><!-- row-area -->
";
            $n_i++;
        } // endif
    } // end foreach
?>
                                    </div><!-- form-area -->
                                    </section><!-- section -->
                                    <section class="section">
                                    <div class="heading">
                                        <h2>Alternative Names (Also Known As)</h2>
                                    </div>
                                    <div class="form-area name-form" id="alternative-names">
<?php
    foreach ($person["names"] as $name) {
        if ($name["Type"] == 'alternate') {
            echo "
                                        <div class=\"row-area\">
                                            <input type=\"hidden\" class=\"form-control\" value=\"{$name["ID"]}\" id=\"name_id_$n_i\" name=\"name_id_$n_i\">
                                            <input type=\"hidden\" class=\"form-control\" value=\"alternate\" id=\"name_type_$n_i\" name=\"name_type_$n_i\">
                                            <div class=\"frame\">
                                                <input type=\"text\" class=\"form-control\" value=\"{$name["Prefix"]}\" id=\"name_prefix_$n_i\" name=\"name_prefix_$n_i\" size=\"4\">
                                                <label for=\"name_prefix_$n_i\">Prefix</label>
                                            </div>
                                            <div class=\"frame\">
                                                <input type=\"text\" class=\"form-control\" value=\"{$name["First"]}\" id=\"name_first_$n_i\" name=\"name_first_$n_i\" size=\"14\">
                                                <label for=\"name_first_$n_i\">First</label>
                                            </div>
                                            <div class=\"frame\">
                                                <input type=\"text\" class=\"form-control\" value=\"{$name["Middle"]}\" id=\"name_middle_$n_i\" name=\"name_middle_$n_i\" size=\"13\">
                                                <label for=\"name_middle_$n_i\">Middle</label>
                                            </div>
                                            <div class=\"frame\">
                                                <input type=\"text\" class=\"form-control\" value=\"{$name["Last"]}\" id=\"name_last_$n_i\" name=\"name_last_$n_i\" size=\"14\">
                                                <label for=\"name_last_$n_i\">Last</label>
                                            </div>
                                            <div class=\"frame\">
                                                <input type=\"text\" class=\"form-control\" value=\"{$name["Suffix"]}\" id=\"name_suffix_$n_i\" name=\"name_suffix_$n_i\" size=\"4\">
                                                <label for=\"name_suffix_$n_i\">Suffix</label>
                                            </div>
                                        </div><!-- row-area -->
";
            $n_i++;
        } // endif
    } // end foreach
?>
                                    </div><!-- form-area -->
                                    <div class="form-area">
                                        <div class="row-area">
                                            <button id="button-add-name" class="btn btn-success btn-save ie-fix"><span>Add New Name</span></button>
                                        </div><!-- row-area -->
                                    </div><!-- form-area -->
                                    </section><!-- section -->
                                    <section class="section">
                                    <div class="heading">
                                        <h2>Birth Information</h2>
                                    </div>
                                    <div class="form-area">
                                        <div class="row-area">
                                            <div class="col-area">
                                                <div class="frame">
                                                    <label class="fixed" for="gender">Gender:</label>
                                                    <select data-placeholder="Select Gender" class="form-control" id="gender" name="gender">
                                                                <option value="Male" <?php if ($person["information"]["Gender"] == "Male") echo "selected";?>>Male</option>
                                                                <option value="Female" <?php if ($person["information"]["Gender"] == "Female") echo "selected";?>>Female</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row-area">
                                            <div class="col-area">
                                                <div class="frame">
                                                    <label class="fixed">Birth Date:</label>
                                                    <input type="text" class="form-control" value="<?=$bdate[1]?>" name="birthmonth" size="2"> /
                                                    <input type="text" class="form-control" value="<?=$bdate[2]?>" name="birthday" size="2"> /
                                                    <input type="text" class="form-control" value="<?=$bdate[0]?>" name="birthyear" size="4">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row-area">
                                            <div class="col-area">
                                                <div class="frame">
                                                    <label class="fixed" for="b_place_id">Birth Place:</label>
                                                    <select data-placeholder="Select Birth Place" class="form-control" id="b_place_id" name="b_place_id">
                                                        <option value="<?=$person["information"]["BirthPlaceID"]?>" selected="selected"><?=$person["information"]["BirthPlaceName"]?></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row-area">
                                            <div class="col-area">
                                                <div class="frame">
                                                    <label class="fixed" for="bpmarriage">Birth Parent Marriage:</label>
                                                    <select data-placeholder="Select Parent Marriage" class="form-control" id="b_marriage_id" name="b_marriage_id">
                                                    <option value="<?=$person["information"]["BiologicalChildOfMarriage"]?>"><?=$person["information"]["ParentMarriageString"]?></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </section><!-- section -->
                                    <section class="section">
                                    <div class="heading">
                                        <h2>Death Information</h2>
                                    </div>
                                    <div class="form-area">
                                        <div class="row-area">
                                            <div class="col-area">
                                                <div class="frame">
                                                    <label class="fixed">Death Date:</label>
                                                    <input type="text" class="form-control" value="<?=$ddate[1]?>" name="deathmonth" size="2"> /
                                                    <input type="text" class="form-control" value="<?=$ddate[2]?>" name="deathday" size="2"> /
                                                    <input type="text" class="form-control" value="<?=$ddate[0]?>" name="deathyear" size="4">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row-area">
                                            <div class="col-area">
                                                <div class="frame">
                                                    <label class="fixed" for="d_place_id">Death Place:</label>
                                                    <select data-placeholder="Select Death Place" class="form-control" id="d_place_id" name="d_place_id">
                                                        <option value="<?=$person["information"]["DeathPlaceID"]?>" selected="selected"><?=$person["information"]["DeathPlaceName"]?></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        </section><!-- section -->
                                        <section class="section">
                                        <div class="heading">
                                            <h2>Notes</h2>
                                        </div>
                                        <div class="notes">
                                        <textarea class="form-control" cols="1" rows="1" id="personal_notes" name="personal_notes"><?=$person["information"]["PrivateNotes"]?></textarea>
                                        </div>
                                        </section><!-- section -->
                                    </div><!-- tab-01 -->
                                    <!-- Temple Rites -->
                                    <div class="tab-pane" id="tab-02">
                                        <section class="section">
                                        <div class="heading">
                                            <h2>Temple Rites Information</h2>
                                        </div>
                                        <div>
                                            <div id="temple-rites-formarea">
<?php
    $r_i = 1;
    foreach ($person["temple_rites"] as $rite) {
        
        $rdate = explode("-", $rite["Date"]);
        if (!isset($rdate[0]) || empty($rdate[0]))
            $rdate[0] = "YYYY";
        if (!isset($rdate[1]) || empty($rdate[1]))
            $rdate[1] = "MM";
        if (!isset($rdate[2]) || empty($rdate[2]))
            $rdate[2] = "DD";

        if ($rite["ProxyID"] == null)
            $rite["ProxyName"] = "";
        if ($rite["AnnointedToID"] == null)
            $rite["AnnointedToName"] = "";
        if ($rite["AnnointedToProxyID"] == null)
            $rite["AnnointedToProxyName"] = "";


?>
                                                <div class="row-area form-area form-block">
                                                    <div class="row-area">
                                                        <input type="hidden" name="tr_id_<?=$r_i?>" id="tr_id_<?=$r_i?>" value="<?=$rite["ID"]?>">
                                                        <div class="frame">
                                                            <label class="fixed" for="tr_type_<?=$r_i?>">Type:</label>
                                                            <select data-placeholder="Select Type" class="form-control" id="tr_type_<?=$r_i?>" name="tr_type_<?=$r_i?>">
                                                                <option value=""></option>
                                                                <option value="baptism" <?php if ($rite["Type"] == "baptism") echo "selected";?>>Baptism</option>
                                                                <option value="endowment" <?php if ($rite["Type"] == "endowment") echo "selected";?>>Endowment</option>
                                                                <option value="secondAnnointing" <?php if ($rite["Type"] == "secondAnnointing") echo "selected";?>>Second Annointing</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed">Date:</label>
                                                            <input type="text" class="form-control" value="<?=$rdate[1]?>" name="tr_date_month_<?=$r_i?>" size="2"> /
                                                            <input type="text" class="form-control" value="<?=$rdate[2]?>" name="tr_date_day_<?=$r_i?>" size="2"> /
                                                            <input type="text" class="form-control" value="<?=$rdate[0]?>" name="tr_date_year_<?=$r_i?>" size="4">
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="tr_place_id_<?=$r_i?>">Place:</label>
                                                            <select data-placeholder="Select Place" class="form-control" id="tr_place_id_<?=$r_i?>" name="tr_place_id_<?=$r_i?>">
                                                                <option value="<?=$rite["PlaceID"]?>" selected="selected"><?=$rite["PlaceName"]?></option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="tr_officiator_person_id_<?=$r_i?>">Officiator:</label>
                                                            <select data-placeholder="Select Officiator" class="form-control" id="tr_officiator_person_id_<?=$r_i?>" name="tr_officiator_person_id_<?=$r_i?>">
                                                                <option value="<?=$rite["OfficiatorID"]?>"><?=$rite["OfficiatorName"]?></option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="tr_officiator_role_<?=$r_i?>">Officiator Role:</label>
                                                            <input type="text" class="form-control" value="<?=$rite["OfficiatorRole"]?>" id="tr_officiator_role_<?=$r_i?>" name="tr_officiator_role_<?=$r_i?>" size="25">
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="tr_proxy_person_id_<?=$r_i?>">Proxy:</label>
                                                            <select data-placeholder="Select Proxy" class="form-control" id="tr_proxy_person_id_<?=$r_i?>" name="tr_proxy_person_id_<?=$r_i?>">
                                                                <option value="<?=$rite["ProxyID"]?>"><?=$rite["ProxyName"]?></option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="tr_annointed_to_person_id_<?=$r_i?>">Annointed To:</label>
                                                            <select data-placeholder="Select Annointed To" class="form-control" id="tr_annointed_to_person_id_<?=$r_i?>" name="tr_annointed_to_person_id_<?=$r_i?>">
                                                                <option value="<?=$rite["AnnointedToID"]?>"><?=$rite["AnnointedToName"]?></option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="tr_annointed_to_proxy_person_id_<?=$r_i?>">Annointed To (Proxy):</label>
                                                            <select data-placeholder="Select Annointed To (Proxy)" class="form-control" id="tr_annointed_to_proxy_person_id_<?=$r_i?>" name="tr_annointed_to_proxy_person_id_<?=$r_i?>">
                                                                <option value="<?=$rite["AnnointedToProxyID"]?>"><?=$rite["AnnointedToProxyName"]?></option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="tr_name_id_<?=$r_i?>">Name as Performed:</label>
                                                            <select data-placeholder="Select Name as Performed" class="form-control" id="tr_name_id_<?=$r_i?>" name="tr_name_id_<?=$r_i?>">
                                                                <option value=""></option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
<?php
    $r_i++;
    } // Temple Rites for loop
?>
                                            </div>
                                        </div><!-- info-form -->
                                        <div class="form-area">
                                            <div class="row-area">
                                                <button id="button-add-rite" class="btn btn-success btn-save ie-fix"><span>Add New Temple Rite Information</span></button>
                                            </div><!-- row-area -->
                                        </div><!-- form-area -->
                                        </section><!-- section -->
                                        <section class="section">
                                        <div class="heading">
                                            <h2>Notes</h2>
                                        </div>
                                        <div class="notes">
                                            <textarea class="form-control" cols="1" rows="1" id="temple_rite_notes" name="temple_rite_notes"></textarea>
                                        </div>
                                        </section><!-- section -->
                                    </div><!-- tab-02 -->
                                    <!-- Non-Marital Sealings -->
                                    <div class="tab-pane" id="tab-03">
                                        <section class="section">
                                        <div class="heading">
                                            <h2>Non-Marital Sealing Information</h2>
                                        </div>
                                        <div>
                                            <div id="nonmarital-sealings-formarea">
<?php
    $s_i = 1;
    foreach ($person["non_marital_sealings"] as $sealing) {
        
        $sdate = explode("-", $sealing["Date"]);
        if (!isset($sdate[0]) || empty($sdate[0]))
            $sdate[0] = "YYYY";
        if (!isset($sdate[1]) || empty($sdate[1]))
            $sdate[1] = "MM";
        if (!isset($sdate[2]) || empty($sdate[2]))
            $sdate[2] = "DD";

        if ($sealing["AdopteeProxyID"] == null)
            $sealing["ProxyName"] = "";
        if ($sealing["MarriageProxyID"] == null)
            $sealing["ProxyMarriageString"] = "";



?>
                                                <div class="row-area form-area form-block">
                                                    <div class="row-area">
                                                    <input type="hidden" name="nms_id_<?=$s_i?>" id="nms_id_<?=$s_i?>" value="<?=$sealing["ID"]?>">
                                                        <div class="frame">
                                                            <label class="fixed" for="nms_type_<?=$s_i?>">Type:</label>
                                                            <select data-placeholder="Select Type" class="form-control" id="nms_type_<?=$s_i?>" name="nms_type_<?=$s_i?>">
                                                                <option value="adoption" <?php if ($sealing["Type"] == "adoption") echo "selected";?>>Adoption</option>
                                                                <option value="secondAnnointing" <?php if ($sealing["Type"] == "secondAnnointing") echo "selected";?>>Second Annointing</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed">Date:</label>
                                                            <input type="text" class="form-control" value="<?=$sdate[1]?>" name="nms_date_month_<?=$s_i?>" size="2"> /
                                                            <input type="text" class="form-control" value="<?=$sdate[2]?>" name="nms_date_day_<?=$s_i?>" size="2"> /
                                                            <input type="text" class="form-control" value="<?=$sdate[0]?>" name="nms_date_year_<?=$s_i?>" size="4">
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="nms_place_id_<?=$s_i?>">Place:</label>
                                                            <select data-placeholder="Select Place" class="form-control" id="nms_place_id_<?=$s_i?>" name="nms_place_id_<?=$s_i?>">
                                                                <option value="<?=$sealing["PlaceID"]?>" selected="selected"><?=$sealing["PlaceName"]?></option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="nms_officiator_person_id_<?=$s_i?>">Officiator:</label>
                                                            <select data-placeholder="Select Officiator" class="form-control" id="nms_officiator_person_id_<?=$s_i?>" name="nms_officiator_person_id_<?=$s_i?>">
                                                                <option value="<?=$sealing["OfficiatorID"]?>"><?=$sealing["OfficiatorName"]?></option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="nms_proxy_person_id_<?=$s_i?>">Proxy:</label>
                                                            <select data-placeholder="Select Proxy" class="form-control" id="nms_proxy_person_id_<?=$s_i?>" name="nms_proxy_person_id_<?=$s_i?>">
                                                            <option value="<?=$sealing["AdopteeProxyID"]?>"><?=$sealing["ProxyName"]?></option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="nms_marriage_id_<?=$s_i?>">Sealed to Marriage:</label>
                                                            <select data-placeholder="Select Sealed to Marriage" class="form-control" id="nms_marriage_id_<?=$s_i?>" name="nms_marriage_id_<?=$s_i?>">
                                                            <option value="<?=$sealing["MarriageID"]?>"><?=$sealing["MarriageString"]?></option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="nms_proxy_marriage_id_<?=$s_i?>">Sealed to Marriage (Proxy):</label>
                                                            <select data-placeholder="Select Sealed to Marriage (Proxy)" class="form-control" id="nms_proxy_marriage_id_<?=$s_i?>" name="nms_proxy_marriage_id_<?=$s_i?>">
                                                            <option value="<?=$sealing["MarriageProxyID"]?>"><?=$sealing["ProxyMarriageString"]?></option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="nms_name_id_<?=$s_i?>">Name as Sealed:</label>
                                                            <select data-placeholder="Select Name as Sealed" class="form-control" id="nms_name_id_<?=$s_i?>" name="nms_name_id_<?=$s_i?>">
                                                                <option value=""></option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
<?php
    $s_i++;
    } // Non marital Sealing for loop
?>
                                            </div>
                                        </div><!-- info-form -->
                                        <div class="form-area">
                                            <div class="row-area">
                                                <button id="button-add-nonmarital" class="btn btn-success btn-save ie-fix"><span>Add New Non-Marital Sealing</span></button>
                                            </div><!-- row-area -->
                                        </div><!-- form-area -->
                                        </section><!-- section -->
                                        <section class="section">
                                        <div class="heading">
                                            <h2>Notes</h2>
                                        </div>
                                        <div class="notes">
                                            <textarea class="form-control" cols="1" rows="1" id="non_marital_notes" name="non_marital_notes"></textarea>
                                        </div>
                                        </section><!-- section -->
                                    </div>
                                    <div class="tab-pane" id="tab-04">
                                        <section class="section">
                                        <div class="heading">
                                            <h2>Marital Sealing Information</h2>
                                        </div>
                                        <div>
                                            <div id="marital-sealings-formarea">
<?php
    $m_i = 1;
    foreach ($person["marriages"] as $marriage) {
        
        $mdate = explode("-", $marriage["MarriageDate"]);
        if (!isset($mdate[0]) || empty($mdate[0]))
            $mdate[0] = "YYYY";
        if (!isset($mdate[1]) || empty($mdate[1]))
            $mdate[1] = "MM";
        if (!isset($mdate[2]) || empty($mdate[2]))
            $mdate[2] = "DD";

        $cdate = explode("-", $marriage["CancelledDate"]);
        if (!isset($cdate[0]) || empty($cdate[0]))
            $cdate[0] = "YYYY";
        if (!isset($cdate[1]) || empty($cdate[1]))
            $cdate[1] = "MM";
        if (!isset($cdate[2]) || empty($cdate[2]))
            $cdate[2] = "DD";

        $divdate = explode("-", $marriage["DivorceDate"]);
        if (!isset($divdate[0]) || empty($divdate[0]))
            $divdate[0] = "YYYY";
        if (!isset($divdate[1]) || empty($divdate[1]))
            $divdate[1] = "MM";
        if (!isset($divdate[2]) || empty($divdate[2]))
            $divdate[2] = "DD";

?>
                                                <div class="row-area form-area form-block">
                                                    <div class="row-area">
                                                        <input type="hidden" name="mar_id_<?=$m_i?>" id="mar_id_<?=$m_i?>" value="<?=$marriage["ID"]?>">
                                                        <div class="frame">
                                                            <label class="fixed" for="mar_type_<?=$m_i?>">Type:</label>
                                                            <select data-placeholder="Select Type" class="form-control" id="mar_type_<?=$m_i?>" name="mar_type_<?=$m_i?>">
                                                                <option value="eternity" <?php if ($marriage["Type"] == "eternity") echo "selected";?>>Sealed for Eternity</option>
                                                                <option value="time" <?php if ($marriage["Type"] == "time") echo "selected";?>>Sealed for Time</option>
                                                                <option value="civil" <?php if ($marriage["Type"] == "civil") echo "selected";?>>Civil Marriage</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="mar_spouse_person_id_<?=$m_i?>">Spouse:</label>
                                                            <select data-placeholder="Select Spouse" class="form-control" id="mar_spouse_person_id_<?=$m_i?>" name="mar_spouse_person_id_<?=$m_i?>">
                                                                <option value="<?=$marriage["SpouseID"]?>" selected="selected"><?php echo $marriage["Last"] . ", " . $marriage["First"] . " " . $marriage["Middle"];?></option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed">Date:</label>
                                                            <input type="text" class="form-control" value="<?=$mdate[1]?>" name="mar_date_month_<?=$m_i?>" size="2"> /
                                                            <input type="text" class="form-control" value="<?=$mdate[2]?>" name="mar_date_day_<?=$m_i?>" size="2"> /
                                                            <input type="text" class="form-control" value="<?=$mdate[0]?>" name="mar_date_year_<?=$m_i?>" size="4">
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed">Divorce Date:</label>
                                                            <input type="text" class="form-control" value="<?=$divdate[1]?>" name="mar_div_month_<?=$m_i?>" size="2"> /
                                                            <input type="text" class="form-control" value="<?=$divdate[2]?>" name="mar_div_day_<?=$m_i?>" size="2"> /
                                                            <input type="text" class="form-control" value="<?=$divdate[0]?>" name="mar_div_year_<?=$m_i?>" size="4">
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed">Cancelled Date:</label>
                                                            <input type="text" class="form-control" value="<?=$cdate[1]?>" name="mar_cancel_month_<?=$m_i?>" size="2"> /
                                                            <input type="text" class="form-control" value="<?=$cdate[2]?>" name="mar_cancel_day_<?=$m_i?>" size="2"> /
                                                            <input type="text" class="form-control" value="<?=$cdate[0]?>" name="mar_cancel_year_<?=$m_i?>" size="4">
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="mar_place_id_<?=$m_i?>">Place:</label>
                                                            <select data-placeholder="Select Place" class="form-control" id="mar_place_id_<?=$m_i?>" name="mar_place_id_<?=$m_i?>">
                                                                <option value="<?=$marriage["PlaceID"]?>" selected="selected"><?=$marriage["PlaceName"]?></option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="mar_officiator_person_id_<?=$m_i?>">Officiator:</label>
                                                            <select data-placeholder="Select Officiator" class="form-control" id="mar_officiator_person_id_<?=$m_i?>" name="mar_officiator_person_id_<?=$m_i?>">
                                                                <option value="<?=$marriage["OfficiatorID"]?>" selected="selected"><?php echo $marriage["OfficiatorLast"] . ", " . $marriage["OfficiatorFirst"];?></option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="mar_proxy_person_id_<?=$m_i?>">Proxy:</label>
                                                            <select data-placeholder="Select Proxy" class="form-control" id="mar_proxy_person_id_<?=$m_i?>" name="mar_proxy_person_id_<?=$m_i?>">
                                                                <option value="<?=$marriage["ProxyID"]?>" selected="selected"><?php echo $marriage["ProxyLast"] . ", " . $marriage["ProxyFirst"];?></option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="mar_spouse_proxy_person_id_<?=$m_i?>">Spouse Proxy:</label>
                                                            <select data-placeholder="Select Spouse Proxy" class="form-control" id="mar_spouse_proxy_person_id_<?=$m_i?>" name="mar_spouse_proxy_person_id_<?=$m_i?>">
                                                                <option value="<?=$marriage["SpouseProxyID"]?>" selected="selected"><?php echo $marriage["SpouseProxyLast"] . ", " . $marriage["SpouseProxyFirst"];?></option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="mar_name_id_<?=$m_i?>">Name as Sealed:</label>
                                                            <select data-placeholder="Select Name as Sealed" class="form-control" id="mar_name_id_<?=$m_i?>" name="mar_name_id_<?=$m_i?>">
                                                                <option value=""></option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
<?php
        $m_i++;
    } // foreach marriage
?>
                                            </div>
                                        </div><!-- info-form -->
                                        <div class="form-area">
                                            <div class="row-area">
                                                <button id="button-add-marriage" class="btn btn-success btn-save ie-fix"><span>Add New Marriage</span></button>
                                            </div><!-- row-area -->
                                        </div><!-- form-area -->
                                        </section><!-- section -->
                                        <section class="section">
                                        <div class="heading">
                                            <h2>Notes</h2>
                                        </div>
                                        <div class="notes">
                                            <textarea class="form-control" cols="1" rows="1" id="notes_marriage" name="notes_marriage"></textarea>
                                        </div>
                                        </section><!-- section -->
                                    </div>
                                    </section><!-- tabs -->
                                </fieldset>
<?php
    echo "<input type=\"hidden\" name=\"n_i\" id=\"n_i\" value=\"$n_i\">";
    echo "<input type=\"hidden\" name=\"r_i\" id=\"r_i\" value=\"$r_i\">";
    echo "<input type=\"hidden\" name=\"s_i\" id=\"s_i\" value=\"$s_i\">";
    echo "<input type=\"hidden\" name=\"m_i\" id=\"m_i\" value=\"$m_i\">";
?>
                            </form>
                        </div>
                    </div><!-- main-area -->
                </div><!-- wrapper -->


                <!-- HIDDEN FORM ENTRIES TO COPY -->

                <div style="display:none;">
                    <div id="name-entry-hidden">
                        <div class="row-area">
                            <input type="hidden" class="form-control" value="NEW" id="name_id_ZZ" name="name_id_ZZ">
                            <input type="hidden" class="form-control" value="alternate" id="name_type_ZZ" name="name_type_ZZ">
                            <div class="frame">
                                <input type="text" class="form-control" value="" id="name_prefix_ZZ" name="name_prefix_ZZ" size="4">
                                <label for="name_prefix_ZZ">Prefix</label>
                            </div>
                            <div class="frame">
                                <input type="text" class="form-control" value="" id="name_first_ZZ" name="name_first_ZZ" size="14">
                                <label for="name_first_ZZ">First</label>
                            </div>
                            <div class="frame">
                                <input type="text" class="form-control" value="" id="name_middle_ZZ" name="name_middle_ZZ" size="13">
                                <label for="name_middle_ZZ">Middle</label>
                            </div>
                            <div class="frame">
                                <input type="text" class="form-control" value="" id="name_last_ZZ" name="name_last_ZZ" size="14">
                                <label for="name_last_ZZ">Last</label>
                            </div>
                            <div class="frame">
                                <input type="text" class="form-control" value="" id="name_suffix_ZZ" name="name_suffix_ZZ" size="4">
                                <label for="name_suffix_ZZ">Suffix</label>
                            </div>
                        </div><!-- row-area -->
                    </div>
                    <div id="rite-entry-hidden">
                        <div class="row-area form-area form-block">
                            <div class="row-area">
                                <input type="hidden" name="tr_id_ZZ" id="tr_id_ZZ" value="NEW">
                                <div class="frame">
                                    <label class="fixed" for="tr_type_ZZ">Type:</label>
                                    <select data-placeholder="Select Type" class="form-control" id="tr_type_ZZ" name="tr_type_ZZ">
                                        <option value=""></option>
                                        <option value="baptism">Baptism</option>
                                        <option value="endowment">Endowment</option>
                                        <option value="secondAnnointing">Second Annointing</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row-area">
                                <div class="frame">
                                    <label class="fixed">Date:</label>
                                    <input type="text" class="form-control" value="MM" name="tr_date_month_ZZ" size="2"> /
                                    <input type="text" class="form-control" value="DD" name="tr_date_day_ZZ" size="2"> /
                                    <input type="text" class="form-control" value="YYYY" name="tr_date_year_ZZ" size="4">
                                </div>
                            </div>
                            <div class="row-area">
                                <div class="frame">
                                    <label class="fixed" for="tr_place_id_ZZ">Place:</label>
                                    <select data-placeholder="Select Place" class="form-control" id="tr_place_id_ZZ" name="tr_place_id_ZZ">
                                    </select>
                                </div>
                            </div>
                            <div class="row-area">
                                <div class="frame">
                                    <label class="fixed" for="tr_officiator_person_id_ZZ">Officiator:</label>
                                    <select data-placeholder="Select Officiator" class="form-control" id="tr_officiator_person_id_ZZ" name="tr_officiator_person_id_ZZ">
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>
                            <div class="row-area">
                                <div class="frame">
                                    <label class="fixed" for="tr_officiator_role_ZZ">Officiator Role:</label>
                                    <input type="text" class="form-control" value="" id="tr_officiator_role_ZZ" name="tr_officiator_role_ZZ" size="25">
                                </div>
                            </div>
                            <div class="row-area">
                                <div class="frame">
                                    <label class="fixed" for="tr_proxy_person_id_ZZ">Proxy:</label>
                                    <select data-placeholder="Select Proxy" class="form-control" id="tr_proxy_person_id_ZZ" name="tr_proxy_person_id_ZZ">
                                    </select>
                                </div>
                            </div>
                            <div class="row-area">
                                <div class="frame">
                                    <label class="fixed" for="tr_annointed_to_person_id_ZZ">Annointed To:</label>
                                    <select data-placeholder="Select Annointed To" class="form-control" id="tr_annointed_to_person_id_ZZ" name="tr_annointed_to_person_id_ZZ">
                                    </select>
                                </div>
                            </div>
                            <div class="row-area">
                                <div class="frame">
                                    <label class="fixed" for="tr_annointed_to_proxy_person_id_ZZ">Annointed To (Proxy):</label>
                                    <select data-placeholder="Select Annointed To (Proxy)" class="form-control" id="tr_annointed_to_proxy_person_id_ZZ" name="tr_annointed_to_proxy_person_id_ZZ">
                                    </select>
                                </div>
                            </div>
                            <div class="row-area">
                                <div class="frame">
                                    <label class="fixed" for="tr_name_id_ZZ">Name as Performed:</label>
                                    <select data-placeholder="Select Name as Performed" class="form-control" id="tr_name_id_ZZ" name="tr_name_id_ZZ">
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="nonmarital-entry-hidden">
                        <div class="row-area form-area form-block">
                            <div class="row-area">
                                <input type="hidden" name="nms_id_ZZ" id="nms_id_ZZ" value="NEW">
                                <div class="frame">
                                    <label class="fixed" for="nms_type_ZZ">Type:</label>
                                    <select data-placeholder="Select Type" class="form-control" id="nms_type_ZZ" name="nms_type_ZZ">
                                        <option value="adoption">Adoption</option>
                                        <option value="secondAnnointing">Second Annointing</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row-area">
                                <div class="frame">
                                    <label class="fixed">Date:</label>
                                    <input type="text" class="form-control" value="MM" name="nms_date_month_ZZ" size="2"> /
                                    <input type="text" class="form-control" value="DD" name="nms_date_day_ZZ" size="2"> /
                                    <input type="text" class="form-control" value="YYYY" name="nms_date_year_ZZ" size="4">
                                </div>
                            </div>
                            <div class="row-area">
                                <div class="frame">
                                    <label class="fixed" for="nms_place_id_ZZ">Place:</label>
                                    <select data-placeholder="Select Place" class="form-control" id="nms_place_id_ZZ" name="nms_place_id_ZZ">
                                    </select>
                                </div>
                            </div>
                            <div class="row-area">
                                <div class="frame">
                                    <label class="fixed" for="nms_officiator_person_id_ZZ">Officiator:</label>
                                    <select data-placeholder="Select Officiator" class="form-control" id="nms_officiator_person_id_ZZ" name="nms_officiator_person_id_ZZ">
                                    </select>
                                </div>
                            </div>
                            <div class="row-area">
                                <div class="frame">
                                    <label class="fixed" for="nms_proxy_person_id_ZZ">Proxy:</label>
                                    <select data-placeholder="Select Proxy" class="form-control" id="nms_proxy_person_id_ZZ" name="nms_proxy_person_id_ZZ">
                                    </select>
                                </div>
                            </div>
                            <div class="row-area">
                                <div class="frame">
                                    <label class="fixed" for="nms_marriage_id_ZZ">Sealed to Marriage:</label>
                                    <select data-placeholder="Select Sealed to Marriage" class="form-control" id="nms_marriage_id_ZZ" name="nms_marriage_id_ZZ">
                                    </select>
                                </div>
                            </div>
                            <div class="row-area">
                                <div class="frame">
                                    <label class="fixed" for="nms_proxy_marriage_id_ZZ">Sealed to Marriage (Proxy):</label>
                                    <select data-placeholder="Select Sealed to Marriage (Proxy)" class="form-control" id="nms_proxy_marriage_id_ZZ" name="nms_proxy_marriage_id_ZZ">
                                    </select>
                                </div>
                            </div>
                            <div class="row-area">
                                <div class="frame">
                                    <label class="fixed" for="nms_name_id_ZZ">Name as Sealed:</label>
                                    <select data-placeholder="Select Name as Sealed" class="form-control" id="nms_name_id_ZZ" name="nms_name_id_ZZ">
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="marriage-entry-hidden">
                                                <div class="row-area form-area form-block">
                                                    <div class="row-area">
                                                        <input type="hidden" name="mar_id_ZZ" id="mar_id_ZZ" value="NEW">
                                                        <div class="frame">
                                                            <label class="fixed" for="mar_type_ZZ">Type:</label>
                                                            <select data-placeholder="Select Type" class="form-control" id="mar_type_ZZ" name="mar_type_ZZ">
                                                                <option value="eternity">Sealed for Eternity</option>
                                                                <option value="time">Sealed for Time</option>
                                                                <option value="civil">Civil Marriage</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="mar_spouse_person_id_ZZ">Spouse:</label>
                                                            <select data-placeholder="Select Spouse" class="form-control" id="mar_spouse_person_id_ZZ" name="marriages1-spouse">
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed">Date:</label>
                                                            <input type="text" class="form-control" value="MM" name="mar_date_month_ZZ" size="2"> /
                                                            <input type="text" class="form-control" value="DD" name="mar_date_day_ZZ" size="2"> /
                                                            <input type="text" class="form-control" value="YYYY" name="mar_date_year_ZZ" size="4">
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed">Divorce Date:</label>
                                                            <input type="text" class="form-control" value="MM" name="mar_div_month_ZZ" size="2"> /
                                                            <input type="text" class="form-control" value="DD" name="mar_div_day_ZZ" size="2"> /
                                                            <input type="text" class="form-control" value="YYYY" name="mar_div_year_ZZ" size="4">
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed">Cancelled Date:</label>
                                                            <input type="text" class="form-control" value="MM" name="mar_cancel_month_ZZ" size="2"> /
                                                            <input type="text" class="form-control" value="DD" name="mar_cancel_day_ZZ" size="2"> /
                                                            <input type="text" class="form-control" value="YYYY" name="mar_cancel_year_ZZ" size="4">
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="mar_place_id_ZZ">Place:</label>
                                                            <select data-placeholder="Select Place" class="form-control" id="mar_place_id_ZZ" name="mar_place_id_ZZ">
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="mar_officiator_person_id_ZZ">Officiator:</label>
                                                            <select data-placeholder="Select Officiator" class="form-control" id="mar_officiator_person_id_ZZ" name="mar_officiator_person_id_ZZ">
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="mar_proxy_person_id_ZZ">Proxy:</label>
                                                            <select data-placeholder="Select Proxy" class="form-control" id="mar_proxy_person_id_ZZ" name="mar_proxy_person_id_ZZ">
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="mar_spouse_proxy_person_id_ZZ">Spouse Proxy:</label>
                                                            <select data-placeholder="Select Spouse Proxy" class="form-control" id="mar_spouse_proxy_person_id_ZZ" name="mar_spouse_proxy_person_id_ZZ">
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="mar_name_id_ZZ">Name as Sealed:</label>
                                                            <select data-placeholder="Select Name as Sealed" class="form-control" id="mar_name_id_ZZ" name="mar_name_id_ZZ">
                                                                <option value=""></option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                    </div>

                </div>
                <!--<script type="text/javascript">
                    customForm.customForms.replaceAll();
                    </script>-->
                </body>
            </html>
