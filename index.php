<?php

include_once("base.php");
include_once("utils.php");

if(isset($_GET["key"])) {
	if(preg_match("/^([a-zA-Z0-9]+)!$/", $_GET["key"], $m)) {
		$url_info  = get_url_info($m[1], gather_meta());
		echo "<ul>";
		foreach(array_keys($url_info) as $k) {
			if($k == "log") {
				continue;
			}
			echo "<li><b>$k</b>: ".$url_info[$k]."</il>";
		}
		echo "<li><b>entry log</b>: (".count($url_info["log"]).")</li>";
		echo "<ul>";
		foreach($url_info["log"] as $l) {
			echo "<li>".$l["tstamp"]."</li>";
		}
		echo "</ul>";
		echo "</ul>";
		die();
	} else {
		$url = get_url($_GET["key"], gather_meta());
		if($url["status"] == "OK") {
			echo "you want to go to: ".$url["url"];
			die();
		}	
	}
	echo "something went wrong, message: ".$url["cause"];
}

if(isset($_POST["url"])) {
	//store url
	$post_data = array(
		"max_hits" => $_POST["max_hits"],
		"notes"    => $_POST["notes"],
		"email"    => $_POST["email"]
	);
	$url = $_POST["url"];
	$strkey = store_url($url, $post_data, gather_meta());
	$uurl = complete_url($strkey);
	echo "Ok, now your url ( $url ) has a new code: <a href='$uurl'>$strkey</a>,<br>\n$uurl<br>\n";
	echo "The stats url is $surl";
	//show result
}

?>
<script>
function validate(form) {
	url = document.getElementById("f_url").value;
	re  = /(http|https):\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/\?%&=]*)?/
	if(re.test(url)) {
		return true;
	}
	alert("c'mon!! \""+url+"\" is not a valid URL");
	return false;
}
</script>

<style>
body {
	font-family: verdana;
	font-size: 14px;
}

form {
	
}
</style>


<form onsubmit="return validate(this);" action="index.php" method="post">
<div>url: <input type=text name=url id=f_url></div>
<div># hits allowed: <input type=text name=max_hits size=3><small>(0/blank for unlimited)</small></div>
<div>notes?<br><textarea name=notes></textarea></div>
<input type=submit>
</form>