<?php

/*
$host = "localhost";
$user = "db76035_short";
$pass = "c4Blevisi0n";
$db   = "db76035_short";
*/

$host = "prueba.kamikazelab.com";
$user = "pruebas_user";
$pass = "kamikaze";
$db   = "short_db";

$link = mysql_connect($host, $user, $pass);
$db_selected = mysql_select_db($db, $link);
if(!$db_selected) {
	die('[sql] problem selecting database<br>'.mysql_error());
}

/**
 * DEFINITIONS
 */

DEFINE("MAIN_URL","http://pruebas.kamikazelab.com/short/index.php?key=%s");

?>