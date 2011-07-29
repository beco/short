<?php

/*
$host = "localhost";
$user = "db76035_short";
$pass = "c4Blevisi0n";
$db   = "db76035_short";
*/

$host = "127.0.0.1";
$user = "root";
$pass = "root";
$db   = "short";

$link = mysql_connect($host, $user, $pass);
$db_selected = mysql_select_db($db, $link);
if(!$db_selected) {
	die('[sql] problem selecting database<br>'.mysql_error());
}

/**
 * DEFINITIONS
 */

DEFINE("MAIN_URL","http://127.0.0.1/short/index.php?key=%s");

?>