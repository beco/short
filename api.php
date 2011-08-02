<?php

include_once ("base.php");
include_once ("utils.php");

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
	$new_XML = generate_xml($url_data,"url");
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


?>