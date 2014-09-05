<html>
<head>
<title>People</title>
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="/nauvoo/css/style.css"/>
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.2/css/jquery.dataTables.css"/>
  
<!-- jQuery -->
<script type="text/javascript" charset="utf8" src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
  
<!-- DataTables -->
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.2/js/jquery.dataTables.js"></script>

</head>
<body>
<script>
$(document).ready( function () {
    $('#datatable').DataTable( {paging: true, ajax: "/nauvoo/api/people.php", deferRender: true});
} );
</script>
<h1>People</h1>
<?php
echo "<table id='datatable' class='display'>";
echo "<thead><tr><th>ID</th><th>First</th><th>Middle</th><th>Last</th><th>Birth Date</th><th>Death Date</th><th>Gender</th><th>Birth Place ID</th></tr></thead>";
echo "</table>";
?>
</body>
</html>
