<?php

date_default_timezone_set('America/Chicago');
$SchedID = 0;
$day = date('D');
$time = date('Hi');
$daybefore = "";

switch ($day) {
    case "Mon":
        $daybefore = "Sun";
        break;
    case "Tue":
        $daybefore = "Mon";
        break;
	case "Wed":
        $daybefore = "Tue";
        break;
	case "Thu":
        $daybefore = "Wed";
        break;
	case "Fri":
        $daybefore = "Thu";
        break;
	case "Sat":
        $daybefore = "Fri";
        break;
	case "Sun":
        $daybefore = "Sat";
        break;
}


include('settings.php');
$con = dbConnect();
dbSelect($con);

$mysqlQuery = "SELECT * FROM  schedule WHERE  active = '1'";
$result = mysql_query($mysqlQuery);
while($row = mysql_fetch_array($result))
  {
	$SchedID = $row['id'];
  }


for ($i = 1; $i <= 8; $i++) {
    $mysqlQuery = "SELECT * FROM  sprinklers WHERE  SchedID = '" . $SchedID . "' AND  Zone = '" . $i . "' AND  " . $day . " = 1";
	$result = mysql_query($mysqlQuery);
	$count = 0;
	while($row = mysql_fetch_array($result))
	  {
		$length = $row['Length'];
		$lengthminutes = $length % 60;
		$lengthhours = ($length-$lengthminutes)/60;
		$timeadd = ($lengthhours*100)+$lengthminutes;
		$starttime = $row['StartTime'];
		if($time >= $starttime && $time <= ($starttime + $timeadd)){
			$count ++;
		}
	  }
	  
	$mysqlQuery2 = "SELECT * FROM  sprinklers WHERE  SchedID = '" . $SchedID . "' AND  Zone = '" . $i . "' AND  " . $daybefore . " = 1";
	$result2 = mysql_query($mysqlQuery2);
	while($row = mysql_fetch_array($result2))
	  {
		$length = $row['Length'];
		$lengthminutes = $length % 60;
		$lengthhours = ($length-$lengthminutes)/60;
		$timeadd = ($lengthhours*100)+$lengthminutes;
		$starttime = $row['StartTime'];
		
		if(($starttime + $timeadd) >= 2400){
			$endtime = ($starttime + $timeadd)-2400;
			if($time >= 0000 && $time <= $endtime){
				$count ++;
			}
		}
		
		
	  }  
	  
	echo '[';
	echo $count;
	echo ']';
}

mysql_close($con);
?>