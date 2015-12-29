<?php
include_once("config.php");

function usernameExists($username) {
	$DB = new Data;
	$getSQL = $DB->getData("Member_Users", "Active", array("Username_Clean" => $username), "LIMIT 1");
	if(is_array($getSQL) && count($getSQL) > 0){
		return true;
	} else {
		return false;
	}
}

function emailExists($email) {
	$DB = new Data;
	$getSQL = $DB->getData("Member_Users", "Active", array("Email" => $email), "LIMIT 1");
	if(is_array($getSQL) && count($getSQL) > 0){
		return true;
	} else {
		return false;
	}
}

//Function lostpass var if set will check for an active account.
function validateActivationToken($token,$lostpass=NULL) {
	// global $db,$db_table_prefix;
	$DB = new Data;
	$token = trim($token);
	if($lostpass == NULL){
		$getSQL = $DB->getData("Member_Users", "ActivationToken",
		array(
			"Active" => "0",
			"ActivationToken" => $token
		), "LIMIT 1");
	} else {
		global $pdo_db;
		$getSQL = $pdo_db->prepare("SELECT * FROM Member_Users WHERE Active = 1 AND ActivationToken = :acttoken AND LostPasswordRequest = 1");
		$getSQL->bindParam(":acttoken", $token);
		$getSQL->execute();
		$getSQL->setFetchMode(PDO::FETCH_ASSOC);
		$getSQL = $getSQL->fetchAll();
	}

	if(is_array($getSQL) && count($getSQL) > 0){
		return true;
	} else {
		return false;
	}
}


function setUserActive($token) {
	$token = trim($token);
	$DB = new Data;
	$updateSQL = $DB->updateData("Member_Users",
	array(
		"Active" => "1"
	),
	array(
		"ActivationToken" => $token
	));

	return($updateSQL);
}

//You can use a activation token to also get user details here
function fetchUserDetails($username=NULL,$token=NULL) {
	$DB = new Data;
	if($username != NULL) {
		$getSQL = $DB->getData("Member_Users", "*", array("Username_Clean" => $username), "LIMIT 1");
	} else {
		$getSQL = $DB->getData("Member_Users", "*", array("ActivationToken" => $token), "LIMIT 1");
	}

	return ($getSQL[0]);
}

function fetchUserDetailswithEmail($email=NULL, $token=NULL) {
	$DB = new Data;
	if($email != NULL) {
		$getSQL = $DB->getData("Member_Users", "*", array("Email" => $email), "LIMIT 1");
	} else {
		$getSQL = $DB->getData("Member_Users", "*", array("ActivationToken" => $token), "LIMIT 1");
	}

	return ($getSQL[0]);
}

function flagLostPasswordRequest($email,$value) {
	$DB = new Data;
	$updateSQL = $DB->updateData("Member_Users",
	array(
		"LostPasswordRequest" => $value
	),
	array(
		"Email" => $email
	));

	return($updateSQL);
}

function updatePasswordFromToken($pass,$token) {
	$new_activation_token = generateActivationToken();

	$DB = new Data;
	$updateSQL = $DB->updateData("Member_Users",
	array(
		"Password" => $pass,
		"ActivationToken" => $new_activation_token
	),
	array(
		"ActivationToken" => $token
	));

	return($updateSQL);
}

function emailUsernameLinked($email,$username) {
	$DB = new Data;
	$getSQL = $DB->getData("Member_Users", "Username, Email", array("Username_Clean" => $username, "Email" => $email), "LIMIT 1");

	if(is_array($getSQL) && count($getSQL) > 0){
		return true;
	} else {
		return false;
	}
}


function isUserLoggedIn() {
	global $loggedInUser,$db,$db_table_prefix;

	if($loggedInUser == NULL){
		return false;
	} else {
		global $pdo_db;
		$getdata = $pdo_db->prepare("SELECT * FROM Member_Users WHERE User_ID = :userid AND Password = :password AND Active = 1");
		$getdata->bindParam(":userid", $loggedInUser->user_id);
		$getdata->bindParam(":password", $loggedInUser->hash_pw);
		$getdata->execute();
		$getdata->setFetchMode(PDO::FETCH_ASSOC);
		$getdata = $getdata->fetchAll();
		//Query the database to ensure they haven't been removed or possibly banned?
		if(is_array($getdata) && count($getdata) > 0){
			return true;
		} else {
			//No result returned kill the user session, user banned or deleted
			$loggedInUser->userLogOut();
			return false;
		}
	}
}

//This function should be used like num_rows, since the PHPBB Dbal doesn't support num rows we create a workaround
function returns_result($sql) {
	global $db;

	$count = 0;
	$result = $db->sql_query($sql);

	while ($row = $db->sql_fetchrow($result)){
	  $count++;
	}

	$db->sql_freeresult($result);

	return ($count);
}

//Generate an activation key
function generateActivationToken() {
	$gen;

	do{
		$gen = md5(uniqid(mt_rand(), false));
	} while(validateActivationToken($gen));

	return $gen;
}

function updateLastActivationRequest($new_activation_token,$username,$email) {
	// global $db,$db_table_prefix;

	// $sql = "UPDATE ".$db_table_prefix."Users
	//  		SET ActivationToken = '".$new_activation_token."',
	// 		LastActivationRequest = '".time()."'
	// 		WHERE Email = '".$db->sql_escape(sanitize($email))."'
	// 		AND
	// 		Username_Clean = '".$db->sql_escape(sanitize($username))."'";
	//
	// return ($db->sql_query($sql));

	$DB = new Data;
	$updateSQL = $DB->updateData("Member_Users",
	array(
		"ActivationToken" => $new_activation_token,
		"LastActivationRequest" => time()
	),
	array(
		"Email" => $email,
		"Username_Clean" => $username
	));

	return($updateSQL);
}
?>
