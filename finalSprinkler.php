<?php
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
if(isset($_POST['zone'])){
	$Zone = $_POST['zone'];
}else{
	$Zone = 0;
}

if(isset($_POST['StartTime'])){
	$StartTime = str_replace(":", "", $_POST['StartTime']);
}else{
	$StartTime = "0000";
}

if(isset($_POST['Length'])){
	$Length = $_POST['Length'];
}else{
	$Length = 5;
}

if(isset($_POST['Mon'])){
	$Mon = $_POST['Mon'];
}else{
	$Mon = 0;
}
if(isset($_POST['Tue'])){
	$Tue = $_POST['Tue'];
}else{
	$Tue = 0;
}
if(isset($_POST['Wed'])){
	$Wed = $_POST['Wed'];
}else{
	$Wed = 0;
}
if(isset($_POST['Thu'])){
	$Thu = $_POST['Thu'];
}else{
	$Thu = 0;
}
if(isset($_POST['Fri'])){
	$Fri = $_POST['Fri'];
}else{
	$Fri = 0;
}
if(isset($_POST['Sat'])){
	$Sat = $_POST['Sat'];
}else{
	$Sat = 0;
}
if(isset($_POST['Sun'])){
	$Sun = $_POST['Sun'];
}else{
	$Sun = 0;
}

if(isset($_POST['append'])){
	$append = $_POST['append'];
}else{
	$append = 0;
}

if(isset($_POST['id'])){
	$id = $_POST['id'];
}else{
	$id = 0;
}

if($append != 0){
	mysql_query("INSERT INTO sprinklers (SchedID, Zone, StartTime, Length, Mon, Tue, Wed, Thu, Fri, Sat, Sun) VALUES ('" . $append . "', '" . $Zone . "', '" . $StartTime . "', '" . $Length . "', '" . $Mon . "', '" . $Tue . "', '" . $Wed . "', '" . $Thu . "', '" . $Fri . "', '" . $Sat . "', '" . $Sun . "')");
}

if($id != 0){
	mysql_query("UPDATE sprinklers SET Mon='" . $Mon . "', Tue='" . $Tue . "', Wed='" . $Wed . "', Thu='" . $Thu . "', Fri='" . $Fri . "', Sat='" . $Sat . "', Sun='" . $Sun . "', StartTime='" . $StartTime . "', Length='" . $Length . "', Zone='" . $Zone . "' WHERE ID='" . $id . "' ");
}

echo '<head>
<meta http-equiv="REFRESH" content="0;url=editSchedule.php"></HEAD>';
}
?>