<?php
date_default_timezone_set('America/Chicago');
include('settings.php');
$con = dbConnect();
dbSelect($con);
$auth = false;
if(isset($_COOKIE["token"])){
	$mysqlQuery = "SELECT * FROM  users WHERE  token = '" . mysql_real_escape_string($_COOKIE["token"]) . "'";
	$result = mysql_query($mysqlQuery);
	while($row = mysql_fetch_array($result))
	  {
		$token_expire = $row['token_expire'];
		if(time() < $token_expire){
			$auth = true;
		}
	  }
}

if(!$auth){
	echo '<head><meta http-equiv="REFRESH" content="0;url=login.php"></HEAD>';
}else{

ini_set("display_errors", 0); 
if(isset($_GET['manualOverride'])){
	$manualOverride = $_GET["manualOverride"];
}
if(isset($_GET['action'])){
	$action = $_GET["action"];
}
if(isset($_GET['id'])){
	$id = $_GET["id"];
}

if(isset($_GET['disable'])){
	mysql_query("UPDATE schedule SET active=0 WHERE id='" . $_GET['disable'] . "'");
	echo '<head><meta http-equiv="REFRESH" content="0;url=editSchedule.php"></HEAD>';
}

if(isset($_GET['enable'])){
	$mysqlQuery2 = "SELECT * FROM schedule WHERE active = '1'";
	$result2 = mysql_query($mysqlQuery2);
	while($row2 = mysql_fetch_array($result2))
	  {
		mysql_query("UPDATE schedule SET active=0 WHERE id='" . $row2['id'] . "'");
	  }
	mysql_query("UPDATE sprinklers SET Zone=0 WHERE SchedID='0'");
	mysql_query("UPDATE schedule SET active=1 WHERE id='" . $_GET['enable'] . "'");
	echo '<head><meta http-equiv="REFRESH" content="0;url=editSchedule.php"></HEAD>';
}

if(isset($_GET["add"])){
	mysql_query("INSERT INTO schedule (name) VALUES ('" . $_GET["add"] . "')");
	echo '<head><meta http-equiv="REFRESH" content="0;url=editSchedule.php"></HEAD>';
}

if(isset($_GET["remove"])){
	mysql_query("DELETE FROM schedule WHERE id='" . $_GET["remove"] . "'");
	$mysqlQuery2 = "SELECT * FROM sprinklers WHERE SchedID = '" . $_GET['remove'] . "'";
	$result2 = mysql_query($mysqlQuery2);
	while($row2 = mysql_fetch_array($result2))
	  {
		mysql_query("DELETE FROM sprinklers WHERE ID='" .  $row2['ID'] . "'");
	  }
	  echo '<head><meta http-equiv="REFRESH" content="0;url=editSchedule.php"></HEAD>';
}

if(isset($manualOverride)){
	
	$mysqlQuery = "SELECT * FROM  schedule WHERE  active = '1'";
	$result = mysql_query($mysqlQuery);
	while($row = mysql_fetch_array($result))
	  {
		mysql_query("UPDATE schedule SET active=0 WHERE id='" . $row['id'] . "'");
	  }
	
	$mysqlQuery = "SELECT * FROM  sprinklers WHERE  SchedID = '0'";
	$result = mysql_query($mysqlQuery);
	while($row = mysql_fetch_array($result))
	  {
		$currentZone = $row['Zone'];
	  }
	mysql_query("UPDATE schedule SET active=1 WHERE id='0'");
	
	if($currentZone == $manualOverride){
		mysql_query("UPDATE sprinklers SET Zone=0 WHERE SchedID='0'");
	}else{
		mysql_query("UPDATE sprinklers SET Zone=" . $manualOverride . " WHERE SchedID='0'");
	}
	echo '<head><meta http-equiv="REFRESH" content="0;url=editSchedule.php"></HEAD>';
}
if(isset($action)){
	if($action == 1){
		mysql_query("DELETE FROM sprinklers WHERE ID='" . $id . "'");
	}
	echo '<head><meta http-equiv="REFRESH" content="0;url=editSchedule.php"></HEAD>';
}

echo '<head>
<style type="text/css">
#banner{
	width:700px;
	margin-left: auto;
	margin-right: auto;
	padding-top: 20px;
	padding-bottom: 20px;
	text-align: center;
}

#scheduleBox {
	border-radius: 15px;
	font-family:"Arial", Times, serif;
	font-size:12px;
	background-color:#606060;
	width:700px;
	margin-left: auto;
	margin-right: auto;
	padding-top:10px;
	padding-bottom:10px;
	text-align: center;
}
table{
	font-size:13px;
}
th
{
	padding-right:5px;
	padding-left:5px;
} 
a:link {color:#2e2e2e;}      /* unvisited link */
a:visited {color:#2e2e2e;}  /* visited link */
a:hover {color:#3e3e3e;}  /* mouse over link */
a:active {color:#2e2e2e;}  /* selected link */
</style>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
<script type="text/javascript" src="animatedcollapse.js" ></script>
<script type="text/javascript">


';
$mysqlQuery = "SELECT * FROM  schedule WHERE  id != '0'";
$result = mysql_query($mysqlQuery);
while($row = mysql_fetch_array($result))
  {

	echo 'animatedcollapse.addDiv(\'box' . $row['id'] . '\', \'fade=0,speed=400,group=pets\')
	';
}
echo 'animatedcollapse.init()
</script>
</head>

<body background="bg.jpg"><div id="banner"><img src="banner.png" /></div>';




$mysqlQuery = "SELECT * FROM  schedule WHERE  id != '0'";
$result = mysql_query($mysqlQuery);
while($row = mysql_fetch_array($result))
  {
	if($row['active'] == 1){
		echo '<div id="scheduleBox"><img src="asterisk_orange.png" /><font size=5><b>' . $row['name'] . '</b></font><img src="asterisk_orange.png" />';
	}else{
		echo '<div id="scheduleBox"><font size=5><b>' . $row['name'] . '</b></font>';
	}
	echo '<br><div id="box' . $row['id'] . '">';
	
	
	echo '<center><table style="text-align:center;" border=0><tr><th>Zone</th><th>Start Time</th><th>Length</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th><th>Sun</th></tr>';
	$mysqlQuery2 = "SELECT * FROM  sprinklers WHERE  SchedID = '" . $row['id'] . "'";
	$result2 = mysql_query($mysqlQuery2);
	while($row2 = mysql_fetch_array($result2))
	  {
		$time = $row2['StartTime'][0] . $row2['StartTime'][1] . ':' . $row2['StartTime'][2] . $row2['StartTime'][3];
	  
		echo '<tr><td>' . $row2['Zone'] . '</td><td>' . $time . '</td><td>' . $row2['Length'] . '</td>';
		
		echo '<td><img src="' . $row2['Mon'] . '.png" /></td>';
		echo '<td><img src="' . $row2['Tue'] . '.png" /></td>';
		echo '<td><img src="' . $row2['Wed'] . '.png" /></td>';
		echo '<td><img src="' . $row2['Thu'] . '.png" /></td>';
		echo '<td><img src="' . $row2['Fri'] . '.png" /></td>';
		echo '<td><img src="' . $row2['Sat'] . '.png" /></td>';
		echo '<td><img src="' . $row2['Sun'] . '.png" /></td>';
		
		echo '<td><a href="editSprinkler.php?id=' . $row2['ID'] . '"><img src="pencil.png" /></a></td>';
		echo '<td><a href="editSchedule.php?action=1&id=' . $row2['ID'] . '"><img src="delete.png" /></a></td>';
		echo '</tr>';
	  }
	
	
	echo '</table>';
	echo '</div>';
	if($row['active'] == 0){
		echo '<a href="editSchedule.php?enable=' . $row['id'] . '">Enable Schedule</a> | ';
	}else{
		echo '<a href="editSchedule.php?disable=' . $row['id'] . '">Disable Schedule</a> | ';
	}
	echo '<a href="editSprinkler.php?appendTo=' . $row['id'] . '">Add Sprinkler</a> | ';
	echo '<a href="editSchedule.php?remove=' . $row['id'] . '">Remove Schedule</a> | ';
	echo '<a href="javascript:animatedcollapse.toggle(\'box' . $row['id'] . '\')">Show/Hide</a>';
	echo '</center><br></div><br>';
  }


$activeZone = 0;
$mysqlQuery = "SELECT * FROM  sprinklers WHERE  SchedID = '0'";
$result = mysql_query($mysqlQuery);
while($row = mysql_fetch_array($result))
  {
	$activeZone = $row['Zone'];
  }
echo '
			<div id="scheduleBox">
			<font size=5><b>Manual Control</font></b>
	
			<center><table border=0><tr><th>1</th><th>2</th><th>3</th><th>4</th><th>5</th><th>6</th><th>7</th><th>8</th></tr><tr>';
			echo '<td><a href="editSchedule.php?manualOverride=1"><img src="';
			if($activeZone == 1){
				echo 'enable.png';
			}else{
				echo 'disable.png';
			}
			echo '" /></a></td>';
			
			echo '<td><a href="editSchedule.php?manualOverride=2"><img src="';
			if($activeZone == 2){
				echo 'enable.png';
			}else{
				echo 'disable.png';
			}
			echo '" /></a></td>';
			
			echo '<td><a href="editSchedule.php?manualOverride=3"><img src="';
			if($activeZone == 3){
				echo 'enable.png';
			}else{
				echo 'disable.png';
			}
			echo '" /></a></td>';
			echo '<td><a href="editSchedule.php?manualOverride=4"><img src="';
			if($activeZone == 4){
				echo 'enable.png';
			}else{
				echo 'disable.png';
			}
			echo '" /></a></td>';
			
			echo '<td><a href="editSchedule.php?manualOverride=5"><img src="';
			if($activeZone == 5){
				echo 'enable.png';
			}else{
				echo 'disable.png';
			}
			echo '" /></a></td>';
			
			echo '<td><a href="editSchedule.php?manualOverride=6"><img src="';
			if($activeZone == 6){
				echo 'enable.png';
			}else{
				echo 'disable.png';
			}
			echo '" /></a></td>';
			
			echo '<td><a href="editSchedule.php?manualOverride=7"><img src="';
			if($activeZone == 7){
				echo 'enable.png';
			}else{
				echo 'disable.png';
			}
			echo '" /></a></td>';
			
			echo '<td><a href="editSchedule.php?manualOverride=8"><img src="';
			if($activeZone == 8){
				echo 'enable.png';
			}else{
				echo 'disable.png';
			}
			echo '" /></a></td>';
			
			  echo '</tr></table>
			  <a href="addSchedule.php">Add Schedule</a></center>
			
		</div>
	
</body>

';
}
?>