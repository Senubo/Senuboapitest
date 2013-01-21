<?php
# This function reads your DATABASE_URL config var and returns a connection
# string suitable for pg_connect. Put this in your app.

  
function pg_connection_string_from_database_url() {
  extract(parse_url($_ENV["DATABASE_URL"]));  
//  echo "user=$user password=$pass host=$host dbname=" . substr($path, 1) . " port=$port sslmode=require"; 
//  echo "<BR>";
  return "user=$user password=$pass host=$host dbname=" . substr($path, 1) . " port=$port sslmode=require"; # <- you may want to add sslmode=require there too
}

if((!isset($_REQUEST['skey']))||($_REQUEST['skey']!='mvemjsunp')){
  exit('Invalid use.');
}

$skey = $_REQUEST['skey'];

if(!isset($_POST['query'])){
$query = "";
}
else{
$query = $_POST['query'];
}

?>
<html>
<body>

<form name="htmlform" method="post" action="runsqlquery.php?skey=<?php echo $skey ?>">
<table width="450px">
</tr>
<tr>
 <td valign="top">
  <label for="query">Query Test</label>
 </td>
 <td valign="top">
  <textarea  name="query" cols="40" rows="6"><?php print($query); ?></textarea>
 </td>
 
</tr>
<tr>
 <td colspan="2" style="text-align:center">
  <input type="submit" value="Submit">
 </td>
</tr>
</table>
</form>

<?php

if($query!=""){

  # Here we establish the connection. Yes, that's all.
  $pg_conn = pg_connect(pg_connection_string_from_database_url());

  print("QUERY: $query<BR>");

   $mtime = microtime(); 
   $mtime = explode(" ",$mtime); 
   $mtime = $mtime[1] + $mtime[0]; 
   $starttime = $mtime; 

  $result = pg_query($pg_conn, "$query");

   $mtime = microtime(); 
   $mtime = explode(" ",$mtime); 
   $mtime = $mtime[1] + $mtime[0]; 
   $endtime = $mtime; 
   $totaltime = ($endtime - $starttime); 
   echo "Query executed in ".$totaltime." seconds<BR>"; 
   
  $cmdtuples = pg_affected_rows($result);
  print($cmdtuples . " tuples are affected.<BR><BR>");
  print("Last error: " . pg_errormessage($pg_conn) . "<BR><BR>");

  if (!pg_num_rows($result)) {
    print("No results.<BR>");
  }
  else {
    $i = 0;
    echo "<table border=1><tbody><tr>";
    while ($i < pg_num_fields($result))
    {
      $fieldName = pg_field_name($result, $i);
      echo '<td>' . $fieldName . '</td>';
      $i = $i + 1;
    }
    echo '</tr>';
    while ($row = @ pg_fetch_row($result))
    {
      print "<tr>";
      foreach($row as $data) print "<td> {$data} </td>";
      print "</tr>";
    }
    print "</tbody></table>";
  }
}

?>

  </body>
</html>
