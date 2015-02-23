<!DOCTYPE html>
<!--
    Notes
    -----
    * Need to load all the places into JS localstore, as they will take a while to load.  They are in json at api/get_places.php
    * All person information can be had by querying get_places with a person ID.  This has ids for places.

-->

<?php
    // load the person
    $person = json_decode(file_get_contents("http://ford.cs.virginia.edu/nauvoo/api/edit_person.php?id=".$_GET["id"]), true);

    $bdate = explode("-", $person["information"]["BirthDate"]);
    if (!isset($bdate[0]) && !empty($bdate[0]))
        $bdate[0] = "YYYY";
    if (!isset($bdate[1]) && !empty($bdate[1]))
        $bdate[1] = "MM";
    if (!isset($bdate[2]) && !empty($bdate[2]))
        $bdate[2] = "DD";
    $ddate = explode("-", $person["information"]["DeathDate"]);
    if (!isset($ddate[0]) && !empty($ddate[0]))
        $ddate[0] = "YYYY";
    if (!isset($ddate[1]) && !empty($ddate[1]))
        $ddate[1] = "MM";
    if (!isset($ddate[2]) && !empty($ddate[2]))
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
                            <a href="#" class="back-link">Back to List</a>
                        </div>
                    </div><!-- frame -->
                    <h1>Edit Person</h1>
                </div><!-- page-header -->
                <div class="alert alert-01 alert-success" style="display: none">
                    <p>Successfully saved!</p>
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
                                        <dt class="visible-md visible-lg">Person ID:</dt><dd class="visible-md visible-lg"><?=$person["information"]["ID"]?></dd>
                                        <input type="hidden" name="ID" id="ID" value="<?=$person["information"]["ID"]?>">
                                        </dl>
                                    </div><!-- info-box -->
                                </div>
                            </div><!-- details-bar -->
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
                                            <input type=\"hidden\" class=\"form-control\" value=\"{$name["ID"]}\" id=\"nameid_$n_i\" name=\"nameid_$n_i\">
                                            <div class=\"frame\">
                                                <input type=\"text\" class=\"form-control\" value=\"{$name["Prefix"]}\" id=\"prefix_$n_i\" name=\"prefix_$n_i\" size=\"4\">
                                                <label for=\"prefix_$n_i\">Prefix</label>
                                            </div>
                                            <div class=\"frame\">
                                                <input type=\"text\" class=\"form-control\" value=\"{$name["First"]}\" id=\"first_$n_i\" name=\"first_$n_i\" size=\"14\">
                                                <label for=\"first_$n_i\">First</label>
                                            </div>
                                            <div class=\"frame\">
                                                <input type=\"text\" class=\"form-control\" value=\"{$name["Middle"]}\" id=\"middle_$n_i\" name=\"middle_$n_i\" size=\"13\">
                                                <label for=\"middle_$n_i\">Middle</label>
                                            </div>
                                            <div class=\"frame\">
                                                <input type=\"text\" class=\"form-control\" value=\"{$name["Last"]}\" id=\"last_$n_i\" name=\"last_$n_i\" size=\"14\">
                                                <label for=\"last_$n_i\">Last</label>
                                            </div>
                                            <div class=\"frame\">
                                                <input type=\"text\" class=\"form-control\" value=\"{$name["Suffix"]}\" id=\"suffix_$n_i\" name=\"suffix_$n_i\" size=\"4\">
                                                <label for=\"suffix_$n_i\">Suffix</label>
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
                                            <input type=\"hidden\" class=\"form-control\" value=\"{$name["ID"]}\" id=\"nameid_$n_i\" name=\"nameid_$n_i\">
                                            <div class=\"frame\">
                                                <input type=\"text\" class=\"form-control\" value=\"{$name["Prefix"]}\" id=\"prefix_$n_i\" name=\"prefix_$n_i\" size=\"4\">
                                                <label for=\"prefix_$n_i\">Prefix</label>
                                            </div>
                                            <div class=\"frame\">
                                                <input type=\"text\" class=\"form-control\" value=\"{$name["First"]}\" id=\"first_$n_i\" name=\"first_$n_i\" size=\"14\">
                                                <label for=\"first_$n_i\">First</label>
                                            </div>
                                            <div class=\"frame\">
                                                <input type=\"text\" class=\"form-control\" value=\"{$name["Middle"]}\" id=\"middle_$n_i\" name=\"middle_$n_i\" size=\"13\">
                                                <label for=\"middle_$n_i\">Middle</label>
                                            </div>
                                            <div class=\"frame\">
                                                <input type=\"text\" class=\"form-control\" value=\"{$name["Last"]}\" id=\"last_$n_i\" name=\"last_$n_i\" size=\"14\">
                                                <label for=\"last_$n_i\">Last</label>
                                            </div>
                                            <div class=\"frame\">
                                                <input type=\"text\" class=\"form-control\" value=\"{$name["Suffix"]}\" id=\"suffix_$n_i\" name=\"suffix_$n_i\" size=\"4\">
                                                <label for=\"suffix_$n_i\">Suffix</label>
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
                                                    <label class="fixed">Birth Date:</label>
                                                    <input type="text" class="form-control" value="<?=$bdate[1]?>" name="birthmonth" size="2">
                                                    <input type="text" class="form-control" value="<?=$bdate[2]?>" name="birthday" size="2">
                                                    <input type="text" class="form-control" value="<?=$bdate[0]?>" name="birthyear" size="4">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row-area">
                                            <div class="col-area">
                                                <div class="frame">
                                                    <label class="fixed" for="bpid">Birth Place:</label>
                                                    <select data-placeholder="Select Birth Place" class="form-control" id="bpid" name="bpid">
                                                        <option value=""></option>
                                                        <option value="15">Nauvoo, ILL</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row-area">
                                            <div class="col-area">
                                                <div class="frame">
                                                    <label class="fixed" for="bpmarriage">Birth Parent Marriage:</label>
                                                    <select data-placeholder="Select Parent Marriage" class="form-control" id="bpmarriage" name="bpmarriage">
                                                        <option value=""></option>
                                                        <option value="15">Brigham Young and Miriam Works (civil, 18xx-MM-DD)</option>
                                                        <option value="15">Brigham Young and Mary Angell  (eternal, 18xx-MM-DD)</option>
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
                                                    <input type="text" class="form-control" value="<?=$ddate[1]?>" name="deathmonth" size="2">
                                                    <input type="text" class="form-control" value="<?=$ddate[2]?>" name="deathday" size="2">
                                                    <input type="text" class="form-control" value="<?=$ddate[0]?>" name="deathyear" size="4">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row-area">
                                            <div class="col-area">
                                                <div class="frame">
                                                    <label class="fixed" for="dpid">Death Place:</label>
                                                    <select data-placeholder="Select Death Place" class="form-control" id="dpid" name="dpid">
                                                        <option value=""></option>
                                                        <option value="15">Nauvoo, ILL</option>
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
                                            <textarea class="form-control" cols="1" rows="1"></textarea>
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
                                                <div class="row-area form-area form-block">
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="rites1-type">Type:</label>
                                                            <select data-placeholder="Select Type" class="form-control" id="rites1-type" name="rites1-type">
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
                                                            <input type="text" class="form-control" value="MM" name="rites1-month" size="2">
                                                            <input type="text" class="form-control" value="DD" name="rites1-day" size="2">
                                                            <input type="text" class="form-control" value="YYYY" name="rites1-year" size="4">
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="rites1-pid">Place:</label>
                                                            <select data-placeholder="Select Place" class="form-control" id="rites1-pid" name="rites1-pid">
                                                                <option value=""></option>
                                                                <option value="15">Nauvoo, ILL</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="rites1-officiator">Officiator:</label>
                                                            <select data-placeholder="Select Officiator" class="form-control" id="rites1-officiator" name="rites1-officiator">
                                                                <option value=""></option>
                                                                <option value="15">Brigham Young</option>
                                                                <option value="15">Joseph Smith</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="rites1-officiator-role">Officiator Role:</label>
                                                            <input type="text" class="form-control" value="" id="rites1-officiator-role" name="rites1-officiator-role" size="25">
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="rites1-proxy">Proxy:</label>
                                                            <select data-placeholder="Select Proxy" class="form-control" id="rites1-proxy" name="rites1-proxy">
                                                                <option value=""></option>
                                                                <option value="15">Brigham Young</option>
                                                                <option value="15">Joseph Smith</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="rites1-annointed-to">Annointed To:</label>
                                                            <select data-placeholder="Select Annointed To" class="form-control" id="rites1-annointed-to" name="rites1-annointed-to">
                                                                <option value=""></option>
                                                                <option value="15">Brigham Young</option>
                                                                <option value="15">Joseph Smith</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="rites1-annointed-to-proxy">Annointed To (Proxy):</label>
                                                            <select data-placeholder="Select Annointed To (Proxy)" class="form-control" id="rites1-annointed-to-proxy" name="rites1-annointed-to-proxy">
                                                                <option value=""></option>
                                                                <option value="15">Brigham Young</option>
                                                                <option value="15">Joseph Smith</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="rites1-name">Name as Performed:</label>
                                                            <select data-placeholder="Select Name as Performed" class="form-control" id="rites1-name" name="rites1-name">
                                                                <option value=""></option>
                                                                <option value="15">Brigham Young</option>
                                                                <option value="15">B Young</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
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
                                            <textarea class="form-control" cols="1" rows="1"></textarea>
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
                                                <div class="row-area form-area form-block">
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="sealings1-type">Type:</label>
                                                            <select data-placeholder="Select Type" class="form-control" id="sealings1-type" name="sealings1-type">
                                                                <option value=""></option>
                                                                <option value="adoption">Adoption</option>
                                                                <option value="secondAnnointing">Second Annointing</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed">Date:</label>
                                                            <input type="text" class="form-control" value="MM" name="sealings1-month" size="2">
                                                            <input type="text" class="form-control" value="DD" name="sealings1-day" size="2">
                                                            <input type="text" class="form-control" value="YYYY" name="sealings1-year" size="4">
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="sealings1-pid">Place:</label>
                                                            <select data-placeholder="Select Place" class="form-control" id="sealings1-pid" name="sealings1-pid">
                                                                <option value=""></option>
                                                                <option value="15">Nauvoo, ILL</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="sealings1-officiator">Officiator:</label>
                                                            <select data-placeholder="Select Officiator" class="form-control" id="sealings1-officiator" name="sealings1-officiator">
                                                                <option value=""></option>
                                                                <option value="15">Brigham Young</option>
                                                                <option value="15">Joseph Smith</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="sealings1-proxy">Proxy:</label>
                                                            <select data-placeholder="Select Proxy" class="form-control" id="sealings1-proxy" name="sealings1-proxy">
                                                                <option value=""></option>
                                                                <option value="15">Brigham Young</option>
                                                                <option value="15">Joseph Smith</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="sealings1-sealed-to">Sealed to Marriage:</label>
                                                            <select data-placeholder="Select Sealed to Marriage" class="form-control" id="sealings1-sealed-to" name="sealings1-sealed-to">
                                                                <option value=""></option>
                                                                <option value="15">Brigham Young and Miriam Works (civil, 18xx-MM-DD)</option>
                                                                <option value="15">Brigham Young and Mary Angell  (eternal, 18xx-MM-DD)</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="sealings1-sealed-to-proxy">Sealed to Marriage (Proxy):</label>
                                                            <select data-placeholder="Select Sealed to Marriage (Proxy)" class="form-control" id="sealings1-sealed-to-proxy" name="sealings1-sealed-to-proxy">
                                                                <option value=""></option>
                                                                <option value="15">Brigham Young and Miriam Works (civil, 18xx-MM-DD)</option>
                                                                <option value="15">Brigham Young and Mary Angell  (eternal, 18xx-MM-DD)</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="sealings1-name">Name as Sealed:</label>
                                                            <select data-placeholder="Select Name as Sealed" class="form-control" id="sealings1-name" name="sealings1-name">
                                                                <option value=""></option>
                                                                <option value="15">Brigham Young</option>
                                                                <option value="15">Joseph Smith</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
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
                                            <textarea class="form-control" cols="1" rows="1"></textarea>
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
                                                <div class="row-area form-area form-block">
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="marriages1-type">Type:</label>
                                                            <select data-placeholder="Select Type" class="form-control" id="marriages1-type" name="marriages1-type">
                                                                <option value=""></option>
                                                                <option value="eternity">Eternity</option>
                                                                <option value="time">Time</option>
                                                                <option value="civil">Civil</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="marriages1-spouse">Spouse:</label>
                                                            <select data-placeholder="Select Spouse" class="form-control" id="marriages1-spouse" name="marriages1-spouse">
                                                                <option value=""></option>
                                                                <option value="15">Brigham Young</option>
                                                                <option value="15">Joseph Smith</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed">Date:</label>
                                                            <input type="text" class="form-control" value="MM" name="marriages1-month" size="2">
                                                            <input type="text" class="form-control" value="DD" name="marriages1-day" size="2">
                                                            <input type="text" class="form-control" value="YYYY" name="marriages1-year" size="4">
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed">Divorce Date:</label>
                                                            <input type="text" class="form-control" value="MM" name="marriages1-divorce-month" size="2">
                                                            <input type="text" class="form-control" value="DD" name="marriages1-divorce-day" size="2">
                                                            <input type="text" class="form-control" value="YYYY" name="marriages1-divorce-year" size="4">
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed">Cancellation Date:</label>
                                                            <input type="text" class="form-control" value="MM" name="marriages1-cancel-month" size="2">
                                                            <input type="text" class="form-control" value="DD" name="marriages1-cancel-day" size="2">
                                                            <input type="text" class="form-control" value="YYYY" name="marriages1-cancel-year" size="4">
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="marriages1-pid">Place:</label>
                                                            <select data-placeholder="Select Place" class="form-control" id="marriages1-pid" name="marriages1-pid">
                                                                <option value=""></option>
                                                                <option value="15">Nauvoo, ILL</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="marriages1-officiator">Officiator:</label>
                                                            <select data-placeholder="Select Officiator" class="form-control" id="marriages1-officiator" name="marriages1-officiator">
                                                                <option value=""></option>
                                                                <option value="15">Brigham Young</option>
                                                                <option value="15">Joseph Smith</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="marriages1-proxy">Proxy:</label>
                                                            <select data-placeholder="Select Proxy" class="form-control" id="marriages1-proxy" name="marriages1-proxy">
                                                                <option value=""></option>
                                                                <option value="15">Brigham Young</option>
                                                                <option value="15">Joseph Smith</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="marriages1-spouse-proxy">Spouse Proxy:</label>
                                                            <select data-placeholder="Select Spouse Proxy" class="form-control" id="marriages1-spouse-proxy" name="marriages1-spouse-proxy">
                                                                <option value=""></option>
                                                                <option value="15">Brigham Young</option>
                                                                <option value="15">Joseph Smith</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row-area">
                                                        <div class="frame">
                                                            <label class="fixed" for="marriages1-name">Name as Sealed:</label>
                                                            <select data-placeholder="Select Name as Sealed" class="form-control" id="marriages1-name" name="marriages1-name">
                                                                <option value=""></option>
                                                                <option value="15">Brigham Young</option>
                                                                <option value="15">Joseph Smith</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
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
                                            <textarea class="form-control" cols="1" rows="1"></textarea>
                                        </div>
                                        </section><!-- section -->
                                    </div>
                                    </section><!-- tabs -->
                                </fieldset>
                            </form>
                        </div>
                    </div><!-- main-area -->
                </div><!-- wrapper -->


                <!-- HIDDEN FORM ENTRIES TO COPY -->

                <div style="display:none;">
                    <div id="name-entry-hidden">
                        <div class="row-area">
                            <input type="hidden" class="form-control" value="NEW" id="ZZnameid" name="ZZnameid">
                            <div class="frame">
                                <input type="text" class="form-control" value="" id="ZZprefix" name="ZZprefix" size="4">
                                <label for="ZZprefix">Prefix</label>
                            </div>
                            <div class="frame">
                                <input type="text" class="form-control" value="" id="ZZfirst" name="ZZfirst" size="14">
                                <label for="ZZfirst">First</label>
                            </div>
                            <div class="frame">
                                <input type="text" class="form-control" value="" id="ZZmiddle" name="ZZmiddle" size="13">
                                <label for="ZZmiddle">Middle</label>
                            </div>
                            <div class="frame">
                                <input type="text" class="form-control" value="" id="ZZlast" name="ZZlast" size="14">
                                <label for="ZZlast">Last</label>
                            </div>
                            <div class="frame">
                                <input type="text" class="form-control" value="" id="ZZsuffix" name="ZZsuffix" size="4">
                                <label for="ZZsuffix">Suffix</label>
                            </div>
                        </div><!-- row-area -->
                    </div>
                    <div id="rite-entry-hidden">
                        <div class="row-area form-area form-block">
                            <div class="row-area">
                                <div class="frame">
                                    <label class="fixed" for="ritesZZ-type">Type:</label>
                                    <select data-placeholder="Select Type" class="form-control" id="ritesZZ-type" name="ritesZZ-type">
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
                                    <input type="text" class="form-control" value="MM" name="ritesZZ-month" size="2">
                                    <input type="text" class="form-control" value="DD" name="ritesZZ-day" size="2">
                                    <input type="text" class="form-control" value="YYYY" name="ritesZZ-year" size="4">
                                </div>
                            </div>
                            <div class="row-area">
                                <div class="frame">
                                    <label class="fixed" for="ritesZZ-pid">Place:</label>
                                    <select data-placeholder="Select Place" class="form-control" id="ritesZZ-pid" name="ritesZZ-pid">
                                        <option value=""></option>
                                        <option value="15">Nauvoo, ILL</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row-area">
                                <div class="frame">
                                    <label class="fixed" for="ritesZZ-officiator">Officiator:</label>
                                    <select data-placeholder="Select Officiator" class="form-control" id="ritesZZ-officiator" name="ritesZZ-officiator">
                                        <option value=""></option>
                                        <option value="15">Brigham Young</option>
                                        <option value="15">Joseph Smith</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row-area">
                                <div class="frame">
                                    <label class="fixed" for="ritesZZ-officiator-role">Officiator Role:</label>
                                    <input type="text" class="form-control" value="" id="ritesZZ-officiator-role" name="ritesZZ-officiator-role" size="25">
                                </div>
                            </div>
                            <div class="row-area">
                                <div class="frame">
                                    <label class="fixed" for="ritesZZ-proxy">Proxy:</label>
                                    <select data-placeholder="Select Proxy" class="form-control" id="ritesZZ-proxy" name="ritesZZ-proxy">
                                        <option value=""></option>
                                        <option value="15">Brigham Young</option>
                                        <option value="15">Joseph Smith</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row-area">
                                <div class="frame">
                                    <label class="fixed" for="ritesZZ-annointed-to">Annointed To:</label>
                                    <select data-placeholder="Select Annointed To" class="form-control" id="ritesZZ-annointed-to" name="ritesZZ-annointed-to">
                                        <option value=""></option>
                                        <option value="15">Brigham Young</option>
                                        <option value="15">Joseph Smith</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row-area">
                                <div class="frame">
                                    <label class="fixed" for="ritesZZ-annointed-to-proxy">Annointed To (Proxy):</label>
                                    <select data-placeholder="Select Annointed To (Proxy)" class="form-control" id="ritesZZ-annointed-to-proxy" name="ritesZZ-annointed-to-proxy">
                                        <option value=""></option>
                                        <option value="15">Brigham Young</option>
                                        <option value="15">Joseph Smith</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row-area">
                                <div class="frame">
                                    <label class="fixed" for="ritesZZ-name">Name as Performed:</label>
                                    <select data-placeholder="Select Name as Performed" class="form-control" id="ritesZZ-name" name="ritesZZ-name">
                                        <option value=""></option>
                                        <option value="15">Brigham Young</option>
                                        <option value="15">B Young</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="nonmarital-entry-hidden">
                        <div class="row-area form-area form-block">
                            <div class="row-area">
                                <div class="frame">
                                    <label class="fixed" for="sealingsZZ-type">Type:</label>
                                    <select data-placeholder="Select Type" class="form-control" id="sealingsZZ-type" name="sealingsZZ-type">
                                        <option value=""></option>
                                        <option value="adoption">Adoption</option>
                                        <option value="secondAnnointing">Second Annointing</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row-area">
                                <div class="frame">
                                    <label class="fixed">Date:</label>
                                    <input type="text" class="form-control" value="MM" name="sealingsZZ-month" size="2">
                                    <input type="text" class="form-control" value="DD" name="sealingsZZ-day" size="2">
                                    <input type="text" class="form-control" value="YYYY" name="sealingsZZ-year" size="4">
                                </div>
                            </div>
                            <div class="row-area">
                                <div class="frame">
                                    <label class="fixed" for="sealingsZZ-pid">Place:</label>
                                    <select data-placeholder="Select Place" class="form-control" id="sealingsZZ-pid" name="sealingsZZ-pid">
                                        <option value=""></option>
                                        <option value="15">Nauvoo, ILL</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row-area">
                                <div class="frame">
                                    <label class="fixed" for="sealingsZZ-officiator">Officiator:</label>
                                    <select data-placeholder="Select Officiator" class="form-control" id="sealingsZZ-officiator" name="sealingsZZ-officiator">
                                        <option value=""></option>
                                        <option value="15">Brigham Young</option>
                                        <option value="15">Joseph Smith</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row-area">
                                <div class="frame">
                                    <label class="fixed" for="sealingsZZ-proxy">Proxy:</label>
                                    <select data-placeholder="Select Proxy" class="form-control" id="sealingsZZ-proxy" name="sealingsZZ-proxy">
                                        <option value=""></option>
                                        <option value="15">Brigham Young</option>
                                        <option value="15">Joseph Smith</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row-area">
                                <div class="frame">
                                    <label class="fixed" for="sealingsZZ-sealed-to">Sealed to Marriage:</label>
                                    <select data-placeholder="Select Sealed to Marriage" class="form-control" id="sealingsZZ-sealed-to" name="sealingsZZ-sealed-to">
                                        <option value=""></option>
                                        <option value="15">Brigham Young and Miriam Works (civil, 18xx-MM-DD)</option>
                                        <option value="15">Brigham Young and Mary Angell  (eternal, 18xx-MM-DD)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row-area">
                                <div class="frame">
                                    <label class="fixed" for="sealingsZZ-sealed-to-proxy">Sealed to Marriage (Proxy):</label>
                                    <select data-placeholder="Select Sealed to Marriage (Proxy)" class="form-control" id="sealingsZZ-sealed-to-proxy" name="sealingsZZ-sealed-to-proxy">
                                        <option value=""></option>
                                        <option value="15">Brigham Young and Miriam Works (civil, 18xx-MM-DD)</option>
                                        <option value="15">Brigham Young and Mary Angell  (eternal, 18xx-MM-DD)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row-area">
                                <div class="frame">
                                    <label class="fixed" for="sealingsZZ-name">Name as Sealed:</label>
                                    <select data-placeholder="Select Name as Sealed" class="form-control" id="sealingsZZ-name" name="sealingsZZ-name">
                                        <option value=""></option>
                                        <option value="15">Brigham Young</option>
                                        <option value="15">Joseph Smith</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="marriage-entry-hidden">
                        <div class="row-area form-area form-block">
                            <div class="row-area">
                                <div class="frame">
                                    <label class="fixed" for="marriagesZZ-type">Type:</label>
                                    <select data-placeholder="Select Type" class="form-control" id="marriagesZZ-type" name="marriagesZZ-type">
                                        <option value=""></option>
                                        <option value="eternity">Eternity</option>
                                        <option value="time">Time</option>
                                        <option value="civil">Civil</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row-area">
                                <div class="frame">
                                    <label class="fixed" for="marriagesZZ-spouse">Spouse:</label>
                                    <select data-placeholder="Select Spouse" class="form-control" id="marriagesZZ-spouse" name="marriagesZZ-spouse">
                                        <option value=""></option>
                                        <option value="15">Brigham Young</option>
                                        <option value="15">Joseph Smith</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row-area">
                                <div class="frame">
                                    <label class="fixed">Date:</label>
                                    <input type="text" class="form-control" value="MM" name="marriagesZZ-month" size="2">
                                    <input type="text" class="form-control" value="DD" name="marriagesZZ-day" size="2">
                                    <input type="text" class="form-control" value="YYYY" name="marriagesZZ-year" size="4">
                                </div>
                            </div>
                            <div class="row-area">
                                <div class="frame">
                                    <label class="fixed">Divorce Date:</label>
                                    <input type="text" class="form-control" value="MM" name="marriagesZZ-divorce-month" size="2">
                                    <input type="text" class="form-control" value="DD" name="marriagesZZ-divorce-day" size="2">
                                    <input type="text" class="form-control" value="YYYY" name="marriagesZZ-divorce-year" size="4">
                                </div>
                            </div>
                            <div class="row-area">
                                <div class="frame">
                                    <label class="fixed">Cancellation Date:</label>
                                    <input type="text" class="form-control" value="MM" name="marriagesZZ-cancel-month" size="2">
                                    <input type="text" class="form-control" value="DD" name="marriagesZZ-cancel-day" size="2">
                                    <input type="text" class="form-control" value="YYYY" name="marriagesZZ-cancel-year" size="4">
                                </div>
                            </div>
                            <div class="row-area">
                                <div class="frame">
                                    <label class="fixed" for="marriagesZZ-pid">Place:</label>
                                    <select data-placeholder="Select Place" class="form-control" id="marriagesZZ-pid" name="marriagesZZ-pid">
                                        <option value=""></option>
                                        <option value="15">Nauvoo, ILL</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row-area">
                                <div class="frame">
                                    <label class="fixed" for="marriagesZZ-officiator">Officiator:</label>
                                    <select data-placeholder="Select Officiator" class="form-control" id="marriagesZZ-officiator" name="marriagesZZ-officiator">
                                        <option value=""></option>
                                        <option value="15">Brigham Young</option>
                                        <option value="15">Joseph Smith</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row-area">
                                <div class="frame">
                                    <label class="fixed" for="marriagesZZ-proxy">Proxy:</label>
                                    <select data-placeholder="Select Proxy" class="form-control" id="marriagesZZ-proxy" name="marriagesZZ-proxy">
                                        <option value=""></option>
                                        <option value="15">Brigham Young</option>
                                        <option value="15">Joseph Smith</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row-area">
                                <div class="frame">
                                    <label class="fixed" for="marriagesZZ-spouse-proxy">Spouse Proxy:</label>
                                    <select data-placeholder="Select Spouse Proxy" class="form-control" id="marriagesZZ-spouse-proxy" name="marriagesZZ-spouse-proxy">
                                        <option value=""></option>
                                        <option value="15">Brigham Young</option>
                                        <option value="15">Joseph Smith</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row-area">
                                <div class="frame">
                                    <label class="fixed" for="marriagesZZ-name">Name as Sealed:</label>
                                    <select data-placeholder="Select Name as Sealed" class="form-control" id="marriagesZZ-name" name="marriagesZZ-name">
                                        <option value=""></option>
                                        <option value="15">Brigham Young</option>
                                        <option value="15">Joseph Smith</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <script type="text/javascript" src="js/jquery-1-10-2-min.js"></script>
                <script type="text/javascript" src="js/chosen.jquery.js"></script>
                <script type="text/javascript" src="js/bootstrap.min.js"></script>
                <script type="text/javascript" src="js/custom-form.js"></script>
                <script type="text/javascript" src="js/custom-form.scrollable.js"></script>
                <script type="text/javascript" src="js/custom-form.file.js"></script>
                <script type="text/javascript" src="js/jquery.mousewheel-3.0.6.pack.js"></script>
                <script type="text/javascript" src="js/jquery.fancybox.pack.js"></script>
                <script type="text/javascript" src="js/scripts.js"></script>
                <script type="text/javascript">
                    customForm.customForms.replaceAll();
                    </script>
                </body>
            </html>
