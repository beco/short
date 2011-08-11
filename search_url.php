<?php
include_once ("base.php");
$hint = $_GET['h'];
$sql = "SELECT instance_id FROM instance WHERE strkey = '".$hint."'";
$res = mysql_query($sql);
if(mysql_result($res,0)!=0) 
	echo '<font color="red">&#10008; Custom URL already used</font>';
else
	echo '<font color="green">&#10004; Custom URL available</font>';
?>