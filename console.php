#!/usr/bin/php
<?php
/**

Command Line tool to work with this shortener.

**/

include_once("base.php");
include_once("utils.php");

$action = $argv[1];
$param  = $argv[2];

if($action == "store"){
	$url = $param;
	if(is_url($url)) {
		$hits   = isset($argv[3]) && is_int($argv[3])?$argv[3]:0;
		$new_id = store_url($url, array("max_hits"=>$hits));
		echo complete_url($new_id);
	} else {
		echo "no maus, eso no es un url:\n$url";
	}
} elseif($action == "ret") {
	$url = get_url($param);
	if($url["status"] != "ERR") {
		echo "your url is:\n    ".$url["url"];
	} else {
		echo $url["cause"];
	}
} elseif($action == "info") {
	echo "here is where the info for $param should be shown";
} else {
	echo "no valid action given, options are:
 - store URL [MAX_HITS]
 - ret CODE
 - info CODE (working on this)";
}
echo "\n";

?>