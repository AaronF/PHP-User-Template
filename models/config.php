<?php
	require_once("settings.php");

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
	} else if(isset($_COOKIE["Template"])) {
		$DB = new Data;
		$getSQL = $DB->getData("Member_Sessions", "sessionData", array("sessionID" => $_COOKIE['Template']));

		if(empty($getSQL)) {
			$loggedInUser = NULL;
			setcookie("Template", "", -parseLength($remember_me_length));
		} else {
			$obj = $getSQL[0];
			$loggedInUser = unserialize($obj["sessionData"]);
		}
	} else {
		//TODO
		// $db->sql_query("DELETE FROM ".$db_table_prefix."Sessions WHERE ".time()." >= (sessionStart+".parseLength($remember_me_length).")");

		// $deleteSQL = $pdo_db->prepare("DELETE FROM Member_Sessions WHERE :curr_time >= (sessionStart+:remember_me_length) ");
		// $deleteSQL->bindValue(":curr_time", time());
		// $deleteSQL->bindValue(":remember_me_length", parseLength($remember_me_length));
		// $return = $deleteSQL->execute();
		//
		// $loggedInUser = NULL;
	}

?>
