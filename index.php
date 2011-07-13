<?php

include_once("base.php");
include_once("utils.php");

if(isset($_GET["key"])) {
	$meta = gather_meta();
	$mode = "goto";
	if(!preg_match("/^([a-zA-Z0-9]+)([!|\.]*)$/",$_GET["key"], $m)){
	    echo "This doesn't seem to be a valid code";
	    die();
	}
	$key = $m[1];
	if($m[2] == "!") {
	    $mode = "info";
	} elseif ($m[2] == ".") {
	    $mode = "preview";
	}
/*
	echo $mode;
	echo "<hr>";
	print_r($m);
*/
	if($mode == "info") {
		$url_info  = get_url_info($m[1], $meta);
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
		$url = get_url($key, $meta);
		if($url["status"] == "OK") {
			if($mode == "preview") {
    			    echo "you want to go to: ".$url["url"];
			} else {
			    header("Location: ".$url["url"]);
			}
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
	$surl = complete_url($strkey."!");
	$msg  = "Ok, now your url ( $url ) has a new code: <a href='$uurl'>$strkey</a>, ";
	$msg .= "<br>\n$uurl ";
	$msg .= "<a href='#' onClick='copyToClipboard(\"".$uurl."\")'>Copy to Clipboard</a><br>\n";
	$msg .= "The stats url is $surl<br>";
	$msg .= "Remember that attaching a '.' at the end of any URL you have the preview of the URL you're about to be redirected.";
	$msg .= "</div>";

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

function copyToClipboard(s) {
	 alert(1);
}

function j(s) {
	 alert(s);
    if ( window.clipboardData && clipboardData.setData )    {
        clipboardData.setData("Text", s);
    } else {
        // You have to sign the code to enable this or allow the action in about:config by changing
        user_pref("signed.applets.codebase_principal_support", true);
        netscape.security.PrivilegeManager.enablePrivilege('UniversalXPConnect');

        var clip Components.classes['@mozilla.org/widget/clipboard;[[[[1]]]]'].createInstance(Components.interfaces.nsIClipboard);
        if (!clip) return;

        // create a transferable
        var trans = Components.classes['@mozilla.org/widget/transferable;[[[[1]]]]'].createInstance(Components.interfaces.nsITransferable);
        if (!trans) return;

        // specify the data we wish to handle. Plaintext in this case.
        trans.addDataFlavor('text/unicode');

        // To get the data from the transferable we need two new objects
        var str = new Object();
        var len = new Object();

        var str = Components.classes["@mozilla.org/supports-string;[[[[1]]]]"].createInstance(Components.interfaces.nsISupportsString);
        var copytext=meintext;
        str.data=copytext;
        trans.setTransferData("text/unicode",str,copytext.length*[[[[2]]]]);
        var clipid=Components.interfaces.nsIClipboard;
        if (!clip) {
            prompt('nah, something went wrong, copy the following text:', s);
            return false;
        }
        clip.setData(trans,null,clipid.kGlobalClipboard);       
    }
}
</script>

<style>
body {
	font-family: verdana;
	font-size: 14px;
}

form {
	
}

#result {
	border: thin solid #333;
	background-color: #555;
	color: #EEE;
	margin: 15px;
	padding: 20px;
}

#result a, a:visited {
	decoration: none;
	color: #999;
}
</style>

<?php
if($msg) {
    echo "<div id='result'>$msg</div>";
}
?>

<form onsubmit="return validate(this);" action="index.php" method="post">
<div>url: <input type=text name=url id=f_url></div>
<div># hits allowed: <input type=text name=max_hits size=3><small>(0/blank for unlimited)</small></div>
<div>notes?<br><textarea name=notes></textarea></div>
<input type=submit>
</form>
