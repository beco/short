<?php

/*
TODO: error handling and propagating
	status=error|ok


-----htaccess	
RewriteRule	^api\.(.*)\.php?(.*)$	api.php?$2&format=$1

#optinal (to see behaivour under tests cases)
RewriteRule ^api.php?(.*)$	api.php?format=plain 
----/htaccess	
	
	
	
*/

include_once ("base.php");
include_once ("utils.php");

$url    = is_url($_GET["url"]);
$format = $_GET["format"]; // NEW
$get_data = array(
	"max_hits" => 0,
	"notes"    => "",
	"email"    => ""
);
$strkey = store_url($url, $get_data, gather_meta());
$result = "";
$url_data = parse_url($url);
if($format == "xml") {
	header("Content-Type: text/xml");
	$new_XML = generate_xml($url_data);
	$result = $new_XML;
} else if($format == "json") 
	$result = json_encode($url_data);
  else 
	$result = complete_url($strkey);


echo $result;

/*

	$get_data = array(
		"max_hits" => 0,
		"notes"    => "",
		"email"    => ""
	);
	$url = is_url($_GET["url"]);
	$strkey = store_url($url, $get_data, gather_meta());





if(isset($_GET["url"])) {
	$get_data = array(
		"max_hits" => 0,
		"notes"    => "",
		"email"    => ""
	);
	$url = is_url($_GET["url"]);
	$strkey = store_url($url, $get_data, gather_meta());
	echo complete_url($strkey);

}

if(isset($_GET["xml"])) {
	header("Content-Type: text/xml");
	$get_data = array(
		"max_hits" => 0,
		"notes"    => "",
		"email"    => ""
	);
	$url = is_url($_GET["xml"]);
	store_url($url, $get_data, gather_meta());
	$url_data = parse_url($_GET["xml"]);
	$new_XML = generate_xml($url_data);
	echo $new_XML;
	
}

if(isset($_GET["json"])) {
	$get_data = array(
		"max_hits" => 0,
		"notes"    => "",
		"email"    => ""
	);
	$url = is_url($_GET["json"]);
	store_url($url, $get_data, gather_meta());
	$url_data = parse_url($_GET["json"]);
	echo json_encode($url_data);
}

*/
?>