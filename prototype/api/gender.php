<?php
header('Content-type: application/json');

// If not a real id, then don't return anything!
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    die();
}

$id = $_GET["id"];

// Connect to the database
$db = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_data user=nauvoo password=p7qNpqygYU");

// Query for the gender
$result = pg_query($db, "SELECT p.\"Gender\" FROM public.\"Person\" p WHERE p.\"ID\"=$id LIMIT 1");
if (!$result) {
    exit;
}
$arr = pg_fetch_all($result);
$gender = $arr[0]["Gender"];

echo "{ \"gender\": \"$gender\" }";

?>
