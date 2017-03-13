<html>
<head>
<title>Query View</title>
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="/nauvoo/css/style.css"/>
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.2/css/jquery.dataTables.css"/>
  
<!-- jQuery -->
<script type="text/javascript" charset="utf8" src="../js/jquery-2.1.1.js"></script>
  
<!-- DataTables -->
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.2/js/jquery.dataTables.js"></script>

</head>
<body>
<script>
$(document).ready( function () {
    $('#datatable').DataTable( {paging: false});
} );
</script>

<h1>Query View</h1>
<?php

include("../database.php");
$db = pg_connect($db_conn_string);

$result = pg_query($db, "

select 
p.\"ID\", p.\"BirthDate\",
cm.\"Last\", cm.\"First\", p.\"Gender\", cm.\"ID\" as \"CivilID\", cm.\"MarriageDate\" as \"CivilDate\",
age(to_timestamp(text(cm.\"MarriageDate\"), 'YYYY-MM-DD'), to_timestamp(text(p.\"BirthDate\"), 'YYYY-MM-DD')) as \"Civil_Age\",
age(to_timestamp(text(cm.\"MarriageDate\"), 'YYYY-MM-DD'), to_timestamp(text(p2.\"BirthDate\"), 'YYYY-MM-DD')) as \"Civil_Spouse_Age\",
p2.\"ID\" as \"Civil_SID\",
cm.\"SLast\" as \"CivilLast\", cm.\"SFirst\" as \"CivilFirst\",p2.\"Gender\" as \"CivilGender\", em.\"ID\" as \"SealID\", em.\"Type\" as \"SealType\", em.\"MarriageDate\" as \"SealDate\",
age(to_timestamp(text(em.\"MarriageDate\"), 'YYYY-MM-DD'), to_timestamp(text(p.\"BirthDate\"), 'YYYY-MM-DD')) as \"Seal_Age\",
age(to_timestamp(text(em.\"MarriageDate\"), 'YYYY-MM-DD'), to_timestamp(text(p3.\"BirthDate\"), 'YYYY-MM-DD')) as \"Seal_Spouse_Age\",
p3.\"ID\" as \"Seal_SID\",
em.\"SLast\" as \"SealLast\", em.\"SFirst\" as \"SealFirst\",p3.\"Gender\" as \"SealGender\"
from \"Person\" p,
    (
            select distinct
            pm1.\"PersonID\",
            n.\"Last\", n.\"First\", n.\"Middle\", 
            pm2.\"PersonID\" as \"SpouseID\",
            n2.\"Last\" as \"SLast\", n2.\"First\" as \"SFirst\", n2.\"Middle\" as \"SMid\", m.\"ID\", m.\"Type\", m.\"MarriageDate\"
            from
            (
                select
                pm1.\"PersonID\", pm1.\"Role\",
                min(m.\"MarriageDate\") as \"MarriageDate\"
                from
                \"Marriage\" m,
                \"PersonMarriage\" pm1
                where pm1.\"MarriageID\" = m.\"ID\" and  pm1.\"Role\" in ('Husband', 'Wife')
                and m.\"Type\" = 'civil'
                group by pm1.\"PersonID\", pm1.\"Role\"
            ) lm,
            \"Marriage\" m, \"PersonMarriage\" pm1, \"PersonMarriage\" pm2, \"Name\" n, \"Name\" n2
            where
            pm1.\"MarriageID\" = m.\"ID\" and pm1.\"Role\" = lm.\"Role\" and pm1.\"PersonID\" = lm.\"PersonID\"
            and pm2.\"MarriageID\" = pm1.\"MarriageID\" and pm2.\"Role\" in ('Husband', 'Wife') and pm2.\"Role\" != pm1.\"Role\"
            and pm1.\"PersonID\" = n.\"PersonID\" and n.\"Type\" = 'authoritative' 
            and pm2.\"PersonID\" = n2.\"PersonID\" and n2.\"Type\" = 'authoritative'
            and m.\"Type\" = 'civil' and m.\"MarriageDate\" = lm.\"MarriageDate\"
    ) cm,
    \"Person\" p2,
    (
            select distinct
            pm1.\"PersonID\",
            n.\"Last\", n.\"First\", n.\"Middle\", 
            pm2.\"PersonID\" as \"SpouseID\",
            n2.\"Last\" as \"SLast\", n2.\"First\" as \"SFirst\", n2.\"Middle\" as \"SMid\", m.\"ID\", m.\"Type\", m.\"MarriageDate\"
            from
            (
                select
                pm1.\"PersonID\", pm1.\"Role\",
                min(m.\"MarriageDate\") as \"MarriageDate\"
                from
                \"Marriage\" m,
                \"PersonMarriage\" pm1
                where pm1.\"MarriageID\" = m.\"ID\" and  pm1.\"Role\" in ('Husband', 'Wife')
                and (m.\"Type\" = 'eternity' or m.\"Type\" = 'time')
                group by pm1.\"PersonID\", pm1.\"Role\"
            ) lm,
            \"Marriage\" m, \"PersonMarriage\" pm1, \"PersonMarriage\" pm2, \"Name\" n, \"Name\" n2
            where
            pm1.\"MarriageID\" = m.\"ID\" and pm1.\"Role\" = lm.\"Role\" and pm1.\"PersonID\" = lm.\"PersonID\"
            and pm2.\"MarriageID\" = pm1.\"MarriageID\" and pm2.\"Role\" in ('Husband', 'Wife') and pm2.\"Role\" != pm1.\"Role\"
            and pm1.\"PersonID\" = n.\"PersonID\" and n.\"Type\" = 'authoritative' 
            and pm2.\"PersonID\" = n2.\"PersonID\" and n2.\"Type\" = 'authoritative'
            and (m.\"Type\" = 'eternity' or m.\"Type\" = 'time') and m.\"MarriageDate\" = lm.\"MarriageDate\"
    ) em,
    \"Person\" p3
where
    p.\"ID\" = cm.\"PersonID\" and
    p2.\"ID\" = cm.\"SpouseID\" and
    p.\"ID\" = em.\"PersonID\" and
    p3.\"ID\" = em.\"SpouseID\"

order by p.\"ID\" asc;



");
if (!$result) {
    echo "An error occurred.\n";
    echo pg_last_error();
    exit;
}

$arr = pg_fetch_all($result);
echo "<table id='datatable' class='display'>";
$json = array();
$first = true;
foreach ($arr as $mar) {
	$resa = array();
    if ($first) $headings = array();

	foreach ($mar as $k=>$v) {
            //array_push($resa,"\"$k\": \"$v\"");
        if ($first) array_push($headings, "$k");
        if ($v == "") {
                array_push($resa, "&nbsp;");
        } else {
                array_push($resa, "$v");
        }
	}
	
	
	if ($first) 
		array_push($json, "<thead><tr><th>" . implode("</th><th>", $headings) . "</th></tr></thead><tbody>");
	array_push($json, "<tr><td>" . implode("</td><td>", $resa) . "</td></tr>");
	$first = false;


}
	echo implode("", $json);

echo "</tbody></table>";
?>
</body>
</html>

