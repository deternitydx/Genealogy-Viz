<html>
<head>
<title>Database Organization</title>
</head>
<body>
<?php
$db = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_data user=nauvoo password=p7qNpqygYU");

$result = pg_query($db, "SELECT table_name, table_type FROM information_schema.tables WHERE table_schema = 'public' AND table_type = 'BASE TABLE' ORDER BY table_name ASC;");
if (!$result) {
      echo "An error occured.\n";
      die();
}

echo "<h1> Nauvoo Database Structure</h1><h2> Institute for Advanced Technology in the Humanities</h2> \n\n";
$tables = pg_fetch_all($result);

foreach ($tables as $table) {
    $tablename = $table["table_name"];

    echo "<h3>$tablename</h3>";

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
        echo "<li><b>{$column['column_name']}</b><br/><em>Type: {$column['data_type']}</em><br/>Description: {$column['description']}</li>\n";
    }

    echo "</ul>\n\n";
}

?>
</body>
</html>
