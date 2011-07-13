<?php

// Common general functions

function is_url($str = "") {
	$pat = "/(http|https):\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/\?%&=]*)?/";
	return preg_match($pat, $str);
}

function sql_clean($str) {
	//TODO!
	return $str;
}

function to_base($number, $base = 10) {
	$res = array();
	while($number >= $base) {
		array_push($res, $number % $base);
		$number = intval($number/$base);
	}
	array_push($res, $number);
	return array_reverse($res);
}

function id_to_key($id) {
	/*
	 * posibly in the future, during an instalation step, this seed shall be
	 * randomized to avoid consecuent id's
	 */
	$seed = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	$code = to_base($id, strlen($seed));
	$ret  = "";
	foreach($code as $c) {
		$ret .= $seed[$c];
	}
	return $ret;
}

function clean_post($arr) {
	/**
	 * cleans the POST (or GET) array to just the information
	 * we need
	 */
	$vars = array(
		"max_hits",
		"notify_email"
	);
	foreach($vars as $v){
		$ret[$v] = $arr[$v];
	}
}

// Action functions

function store_url($str, $post_data, $meta_data = array()) {
	
	$url = sql_clean($str);

	//check if it is already un the database
	$sql = "SELECT url_id FROM url WHERE url = '$url'";
	$res = mysql_query($sql);
	
	//store it if it isn't
	//get the url's id
	$url_id = "";
	if(mysql_affected_rows() == 1) {
		$row = mysql_fetch_assoc($res);
		$url_id = $row["url_id"];
	} else {
		$sql = "INSERT INTO url(url) VALUES('$url')";
		mysql_query($sql);
		$url_id = mysql_insert_id();
	}
	
	//create the new row in the appearances table
	//first we gather the information from the form
	$hits = isset($post_data["max_hits"]) && is_int(0 + $post_data["max_hits"])?$post_data["max_hits"]:0;
	$note = isset($post_data["notes"])?$post_data["notes"]:"";
	$mail = isset($post_data["email"])?$post_data["email"]:"";
	$sql = sprintf(
		"INSERT INTO INSTANCE(url_id, strkey, active, max_hits, notify_email, notes) ".
		"VALUES('%d','%d','1','%d','%s', '%s')",
		$url_id, rand(0,1000), $hits, $mail, $note
	);
	$res = mysql_query($sql);
	if(mysql_error() || mysql_affected_rows() != 1){
		die("can't make the insert of a new instance<br>$sql<br>".mysql_error());
	}
	
	//get a new string id for the new row
	$new_id = mysql_insert_id();
	$str_id = id_to_key($new_id);
	
	$sql = "UPDATE INSTANCE SET strkey = '$str_id' WHERE instance_id = $new_id";
	mysql_query($sql);
	
	//log this new creation
	store_log($new_id, "create", "ok", $meta_data);
	
	//return the string id
	return $str_id;
}

function send_notifications($code, $row) {
	$mail_pattern = "/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/";
	$template = "
Hello, you asked us to send you a notiffication when anyone acccess\n
$
	";
	if(preg_match($mail_pattern, $row["emails"])) {
		
	}
	return true;
}

function store_log($iid, $action, $outcome, $meta = array()){
	if(!preg_match("/create|access/",$action)) {
		return;
	}
	$ip    = isset($meta["ip"])?$meta["ip"]:"";
	$host  = isset($meta["host"])?$meta["host"]:"";
	$agent = isset($meta["agent"])?$meta["agent"]:"";
	$refer = isset($meta["referer"])?$meta["referer"]:"";
	
	$sql = sprintf("INSERT INTO log(instance_id, type, outcome, client_ip, client_host, client_agent) ".
					"VALUES(%d,'%s','%s','%s','%s','%s')",
					$iid, $action, $outcome, $ip, $host, $agent);
	mysql_query($sql);
	if(mysql_error()){
		echo mysql_error()."\n".$sql;
	}
	return mysql_error() == "";
}

function get_url($code = "", $meta = array()){
	$ret = array(
		"code" => $code,
		"status" => "ERR",
		"url" => null,
		"cause" => "something is really fucked up with code: $code, even the error code is NaN"
	);

	if($code != "") {
		// this sql should count on log with outcome = "ok"
		$sql = sprintf("
			SELECT i.instance_id AS iid, u.url AS url, i.active AS active,
				i.max_hits AS max_hits, i.notify_email AS emails, 
				count(l.log_id) AS act_hits
			FROM instance AS i
			LEFT JOIN url AS u
				ON u.url_id = i.url_id
			LEFT JOIN log AS l
				ON i.instance_id = l.instance_id
			WHERE i.strkey = '%s'
			GROUP BY iid
			LIMIT 1", $code);
		$res = mysql_query($sql);
		if(!mysql_error() && mysql_affected_rows() > 0){
			$row = mysql_fetch_assoc($res);
			
			//set of business logic rules
			if($row["active"] == 0) {
				$ret["cause"] = "corresponding link is not active any more";
				store_log($row["iid"],"access","error", $meta);
				return $ret;
			} elseif($row["max_hits"] > 0 && $row["act_hits"] >= $row["max_hits"]) {
				$ret["cause"] = "this link had a certain number of allowed hits which has already been reached";
				store_log($row["iid"],"access","error", $meta);
				return $ret;
			}
			
			if($row["emails"] != "") {
				send_notifications($code, $row);
			}
			$ret["status"] = "OK";
			$ret["cause"]  = "we're all right!";
			$ret["url"]    = $row["url"];
			store_log($row["iid"],"access","ok",$meta);
			return $ret;
			
		}else{
			if(mysql_affected_rows()){
				$ret["cause"] = "seems there is no url associated to this code [error code: 5]\n";
			} else {
				$ret["cause"] = "something is wrong with the db [error code: 4]$sql\n";
				if(mysql_error()) {
				    $ret["cause"] .= "\n mysql said: ".mysql_error();
				}
			}
		}
	}
	return $ret;
}

function complete_url($code) {
	return sprintf(MAIN_URL, $code);
}

function gather_meta() {
	return array(
		"ip"      => $_SERVER["REMOTE_ADDR"],
		"referer" => $_SERVER["HTTP_REFERER"],
		"agent"   => $_SERVER["REMOTE_AGENT"],
		"host"    => $_SERVER["REMOTE_HOST"]
	);
}

function get_url_info($key) {
	$ret = array(
		"status" => "ERR",
		"cause"  => "something went terribly wrong",
	);
	$sql = sprintf("
				SELECT i.instance_id AS iid, u.url AS url, 
					i.created_at AS created_at, 
					i.notes AS notes, count(it.instance_id) AS total, 
					u.url_id AS uid
				FROM instance AS i
				LEFT JOIN url as u
					ON u.url_id = i.url_id
				LEFT JOIN instance as it
					ON it.url_id = i.url_id
				WHERE i.strkey = '%s'
				GROUP BY i.instance_id
			",
			$key);
	$res = mysql_query($sql);
	$row = mysql_fetch_assoc($res);
	if(!mysql_error() && mysql_affected_rows() > 0) {
		$ret["status"]     = "OK";
		$ret["url"]        = $row["url"];
		$ret["count"]      = $row["total"];
		$ret["created_at"] = $row["created_at"];
		$ret["notes"]      = $row["notes"];
		$ret["iid"]        = $row["iid"];
		$ret["log"]        = array();
		unset($ret["cause"]);

		$sql = sprintf("
					SELECT type, tstamp 
					FROM log
					WHERE instance_id = %d",
					$row["iid"]);

		$res = mysql_query($sql);

		$i = 0;

		while($row = mysql_fetch_assoc($res)) {
			$ret["log"][$i++] = array(
				"type" => $row["type"],
				"tstamp" => $row["tstamp"]
			);
		}
	}
	return $ret;
}

?>