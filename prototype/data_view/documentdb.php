<html>
<head>
<title>Database Organization</title>
<link rel="stylesheet" type="text/css" href="/nauvoo/css/style.css"/>
</head>
<body>
<?php
$db = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_data user=nauvoo password=p7qNpqygYU");

// Fetch all tables
$result = pg_query($db, "SELECT table_name, table_type FROM information_schema.tables WHERE table_schema = 'public' AND table_type = 'BASE TABLE' ORDER BY table_name ASC;");
if (!$result) {
      echo "An error occured.\n";
      die();
}
$tables = pg_fetch_all($result);

// Fetch all enums
$result = pg_query($db, "select t.typname, e.enumlabel from pg_type t, pg_enum e where t.oid = e.enumtypid order by t.typname, e.enumlabel ASC");
if (!$result) {
      echo "An error occured.\n";
      die();
}
$tmp = pg_fetch_all($result);

$enums = array();
foreach ($tmp as $t) {
    if (!isset($enums[$t['typname']]))
        $enums[$t['typname']] = array();
    array_push($enums[$t['typname']], $t['enumlabel']);
}


$enum_lookup = array (
    "PersonMarriageRole" => "e_marriage_role",
    "NameType" => "e_name_type",
    "MarriageType" => "e_marriage_type",
    "NonMaritalTempleRitesType" => "e_templerite_type",
    "NonMaritalSealingsType" => "e_sealing_type",
    "PersonGender" => "e_gender"
);

// Display
echo "<h1> Nauvoo Database Structure</h1><h2> Institute for Advanced Technology in the Humanities</h2> \n\n";


foreach ($tables as $table) {
    $tablename = $table["table_name"];

    echo "<h3>$tablename</h3>";

    // Fetch all columns for the table
    $result = pg_query($db, "
            SELECT c.column_name,c.data_type,pgd.description, c.ordinal_position
            FROM information_schema.columns c left outer join
            pg_catalog.pg_statio_all_tables st on (c.table_schema=st.schemaname and c.table_name=st.relname)
              left outer join pg_catalog.pg_description pgd on (pgd.objoid=st.relid and pgd.objsubid=c.ordinal_position)
                   WHERE c.table_name = '$tablename' ORDER BY c.ordinal_position ASC;");

    $columns = pg_fetch_all($result);
    echo "<h4>Columns</h4>\n\n<ul>";
    foreach($columns as $column) {
        if (!isset($column['description'])) $column['description'] = "";
        echo "<li><b>{$column['column_name']}</b>";
        if (!isset($column["data_type"]) || $column["data_type"] != "USER-DEFINED")
            echo "<br/><em>Type: {$column['data_type']}</em>";
        echo "<br/>Description: {$column['description']}";
        if (isset($column["data_type"]) && $column["data_type"] == "USER-DEFINED")
            echo "<br/>Possible values: <em>" . implode(", ", $enums[$enum_lookup[$tablename . $column['column_name']]]) . "</em>";
        echo "</li>\n";
    }

    echo "</ul>\n\n";
}

?>
</body>
</html>
