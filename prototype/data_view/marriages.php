<html>
<head>
<title>Marriages</title>
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="/nauvoo/css/style.css"/>
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.2/css/jquery.dataTables.css"/>
  
<!-- jQuery -->
<script type="text/javascript" charset="utf8" src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
  
<!-- DataTables -->
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.2/js/jquery.dataTables.js"></script>

</head>
<script>
var QueryString = function () {
  // This function is anonymous, is executed immediately and 
  // the return value is assigned to QueryString!
  var query_string = {};
  var query = window.location.search.substring(1);
  var vars = query.split("&");
  for (var i=0;i<vars.length;i++) {
    var pair = vars[i].split("=");
    	// If first entry with this name
    if (typeof query_string[pair[0]] === "undefined") {
      query_string[pair[0]] = pair[1];
    	// If second entry with this name
    } else if (typeof query_string[pair[0]] === "string") {
      var arr = [ query_string[pair[0]], pair[1] ];
      query_string[pair[0]] = arr;
    	// If third or later entry with this name
    } else {
      query_string[pair[0]].push(pair[1]);
    }
  } 
    return query_string;
} ();
$(document).ready( function () {
    var q = "?";
    if (QueryString.idSearch) {
//        dt.column(0).search("^" + QueryString.idSearch + "$", true).draw();
        q += "id=" + QueryString.idSearch;
    }    
    var dt = $('#datatable').DataTable( {paging: true, ajax: "../api/marriages.php" + q, deferRender: true});
} );
</script>
<body>
<h1>Marriages</h1>
<?php
echo "<table id='datatable' class='display'>";
echo "<thead><tr><th>ID</th><th>Marriage Date</th><th>Divorce Date</th><th>Cancelled Date</th><th>Type</th><th>Husband ID</th><th>Husband Last</th><th>Husband First</th>
    <th>Wife ID</th><th>Wife Last</th><th>Wife First</th><th>Children</th></tr></thead>";
echo "</table>";
?>
</body>
</html>

