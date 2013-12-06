<?php
	require_once("settings.php");
	require_once("db/".$dbtype.".php");
	
	ini_set('display_errors', '0');
	error_reporting(E_ALL | E_STRICT);


	$db = new $sql_db();
	if(is_array($db->sql_connect(
		$db_host, 
		$db_user,
		$db_pass,
		$db_name, 
		$db_port,
		false, 
		false
	))) {
		die("Unable to connect to the database");
	}
	
	try {
	    $pdo_db = new PDO("mysql:host=localhost;dbname=$db_name", $db_user, $db_pass);  
	} catch (PDOException $e) {
	    print "Error!: " . $e->getMessage() . "<br/>";
	    die();
	}
	
	if(!isset($language)) $langauge = "en";

	require_once("lang/".$langauge.".php");
	require_once("class.user.php");
	require_once("class.mail.php");
	require_once("funcs.user.php");
	require_once("funcs.general.php");
	require_once("class.newuser.php");
	require_once("class.data.php");

	session_start();
	
	if(isset($_SESSION["Template"]) && is_object($_SESSION["Template"])) {
		$loggedInUser = $_SESSION["Template"];
	} else if(isset($_COOKIE["TemplateUser"])) {
		$db->sql_query("SELECT sessionData FROM ".$db_table_prefix."Sessions WHERE sessionID = '".$_COOKIE['TemplateUser']."'");
		$dbRes = $db->sql_fetchrowset();
		if(empty($dbRes)) {
			$loggedInUser = NULL;
			setcookie("TemplateUser", "", -parseLength($remember_me_length));
		}
		else {
			$obj = $dbRes[0];
			$loggedInUser = unserialize($obj["sessionData"]);
		}
	}
	else {
		$db->sql_query("DELETE FROM ".$db_table_prefix."Sessions WHERE ".time()." >= (sessionStart+".parseLength($remember_me_length).")");
		$loggedInUser = NULL;
	}

?>