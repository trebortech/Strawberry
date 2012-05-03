<?php

function dbConnect(){
	$con = mysql_connect("localhost","root","");
	if (!$con)
	  {
	  die('Could not connect: ' . mysql_error());
	  }
	return $con;
}

function dbSelect($con){
	mysql_select_db("athome", $con);
}

function getWebsite(){
	return "boxxymays.com";
}

?>