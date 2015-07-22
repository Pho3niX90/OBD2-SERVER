<?php
// MySQL Credentials
$db_host = "localhost";
$db_user = "";
$db_pass = "";
$db_name = "";
$db_table= "";

$con = mysql_connect($db_host, $db_user, $db_pass) or die(mysql_error());
mysql_select_db($db_name, $con) or die(mysql_error());


$result = mysql_query("SHOW TABLES LIKE $db_table");
$tableExists = mysql_num_rows($result) > 0;

if (!$tableExists) 
{ //first run
$createSQL = 
'CREATE TABLE '.$db_table.' (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `vehicleid` varchar(255) DEFAULT NULL,
  `latitude` varchar(255) DEFAULT NULL,
  `longitude` varchar(255) DEFAULT NULL,
  `altitude` varchar(255) DEFAULT NULL,
  `timestamp` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1';
mysql_query($createSQL);
}



$result = mysql_query("SHOW COLUMNS FROM $db_table", $con) or die(mysql_error());
if (mysql_num_rows($result) > 0) {
    while ($row = mysql_fetch_assoc($result)) {
        $dbfields[]=($row['Field']);
    }
}
    $keys = array();
    $values = array();
	
	
$handle  = fopen('php://input', 'r');
$rawData = '';
while ($chunk = fread($handle, 1024)) {
    $rawData .= $chunk;
}	
	
$array = (array) json_decode($rawData,true);

  foreach ($array as $k => $v) {
	  if (!is_array($v))
	  {
		  $keys[] = $k;
		  $values[] = $v;
          $submitval = 1;
	  }
	  else if (is_array($v))
	  {
		    foreach ($v as $k2 => $v2) {
		  $keys[] = $k2;
		  $values[] = $v2;
          $submitval = 1;
		  
		    if (!in_array($k2, $dbfields) and $submitval == 1) {
            $sqlalter = "ALTER TABLE $db_table ADD $k2 VARCHAR(255) NOT NULL default '0'";
            mysql_query($sqlalter, $con) or die(mysql_error());
			
        } 
			}
	  }
  }
  
fclose($handle);
		
		
    if ((sizeof($keys) === sizeof($values)) && sizeof($keys) > 0) {
        $sql = "INSERT INTO $db_table (".implode(",", $keys).") VALUES ('".implode("','", $values)."')";
		echo $sql;
        mysql_query($sql, $con) or die(mysql_error());
    }
	mysql_close($con);
echo "OK!";
		
?>
