<?php
	include('settings.php');
	$con = dbConnect();
	dbSelect($con);
	
	if(isset($_POST['username'])){
		if($_POST['username'] == 'admin' && $_POST['password'] == '------'){
			$token = sha1(time());
			$token_expire = time() + 7200;
			mysql_query("UPDATE users SET token='" . $token . "' WHERE id='1'");
			mysql_query("UPDATE users SET token_expire='" . $token_expire . "' WHERE id='1'");
			echo '<head><meta http-equiv="REFRESH" content="0;url=editSchedule.php"></HEAD>';
			setcookie("token", $token, time()+7200);
		}
	}
?>

<form action="login.php" method="post">
Username: <input type="text" name="username" /><br />
Password: <input type="password" name="password" /><br />
<input type="submit" />

</form>