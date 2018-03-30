<?php
require_once("config.php");

function text_limit($str,$limit=10) {
	if(stripos($str," ")){
    	$ex_str = explode(" ",$str);
        if(count($ex_str)>$limit){
            for($i=0;$i<$limit;$i++){
            	$str_s.=$ex_str[$i]." ";
            }
        	return $str_s;
        } else {
        	return $str;
        }
    } else {
    	return $str;
    }
}

function rand_string($length) {
	$str = '';
	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

	$size = strlen( $chars );
	for( $i = 0; $i < $length; $i++ ) {
		$str .= $chars[ rand( 0, $size - 1 ) ];
	}
	return $str;
}

function sanitize($str) {
	return strtolower(strip_tags(trim(($str))));
}

function isValidEmail($email) {
	return preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",trim($email));
}

function minMaxRange($min, $max, $what) {
	if(strlen(trim($what)) < $min) {
	   return true;
	} else if(strlen(trim($what)) > $max) {
	   return true;
	} else {
	   return false;
	}
}

function generateHash($plainText) {
	$salt = substr(str_replace('+', '.', base64_encode(sha1(microtime(true), true))), 0, 22);
	$hash = crypt($plainText, '$2a$13$' . $salt);

	return $hash;
}

function replaceDefaultHook($str) {
	global $default_hooks,$default_replace;
	return (str_replace($default_hooks,$default_replace,$str));
}

function getUniqueCode($length = "") {
	$code = md5(uniqid(rand(), true));
	if ($length != "") {
		return substr($code, 0, $length);
	} else {
		return $code;
	}
}

function errorBlock($errors) {
	if(!count($errors) > 0) {
		return false;
	}
	else {
		echo "<ul>";
		foreach($errors as $error) {
			echo "<li>".$error."</li>";
		}
		echo "</ul>";
	}
}

function lang($key,$markers = NULL) {
	global $lang;

	if($markers == NULL) {
		$str = $lang[$key];
	}
	else {
		//Replace any dyamic markers
		$str = $lang[$key];

		$iteration = 1;

		foreach($markers as $marker) {
			$str = str_replace("%m".$iteration."%",$marker,$str);

			$iteration++;
		}
	}

	//Ensure we have something to return
	if($str == "") {
		return ("No language key found");
	}
	else {
		return $str;
	}
}

function destorySession($name) {
	global $remember_me_length,$loggedInUser,$db,$db_table_prefix;

	if($loggedInUser->remember_me == 0) {
		if(isset($_SESSION[$name])){
			$_SESSION[$name] = NULL;
			unset($_SESSION[$name]);
			$loggedInUser = NULL;
		}
	} else if($loggedInUser->remember_me == 1) {
		if(isset($_COOKIE[$name])){
			$DB = new Data;
			$deleteSQL = $DB->deleteData("Member_Sessions",
			array(
				"sessionID" => $loggedInUser->remember_me_sessid
			));

			setcookie($name, "", time() - parseLength($remember_me_length));
			$loggedInUser = NULL;

		}
	}
}

function updateSessionObj() {
	global $loggedInUser,$db,$db_table_prefix;

	$newObj = serialize($loggedInUser);

	$DB = new Data;
	$updateSQL = $DB->updateData("Member_Sessions",
	array(
		"sessionData" => $newObj
	),
	array(
		"sessionID" => $loggedInUser->remember_me_sessid
	));
}


function parseLength($len) {
	$user_units = strtolower(substr($len, -2));
	$user_time = substr($len, 0, -2);
	$units = array(
		"mi" => 60,
		"hr" => 3600,
		"dy" => 86400,
		"wk" => 604800,
		"mo" => 2592000
	);
	if(!array_key_exists($user_units, $units)) {
		die("Invalid unit of time.");
	} else if(!is_numeric($user_time)) {
		die("Invalid length of time.");
	} else {
		return (int)$user_time*$units[$user_units];
	}
}

?>
