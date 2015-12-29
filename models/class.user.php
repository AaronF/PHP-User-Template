<?php
class loggedInUser {

	public $email = NULL;
	public $hash_pw = NULL;
	public $user_id = NULL;
	public $clean_username = NULL;
	public $display_username = NULL;
	public $remember_me = NULL;
	public $remember_me_sessid = NULL;

	//Last sign in of user
	public function updateLastSignIn(){
		$DB = new Data;
		$updateSQL = $DB->updateData("Member_Users",
		array(
			"LastSignIn" => time()
		),
		array(
			"User_ID" => $this->user_id
		));

		return($updateSQL);
	}

	//Return the timestamp when the user registered
	public function signupTimeStamp(){
		$DB = new Data;
		$getSQL = $DB->getData("Member_Users", "SignUpDate", array("User_ID" => $this->user_id));
		return($getSQL[0]["SignUpDate"]);
	}

	//Update a users password
	public function updatePassword($pass){
		$secure_pass = generateHash($pass);

		$this->hash_pw = $secure_pass;
		if($this->remember_me == 1){
			updateSessionObj();
		}

		$DB = new Data;
		$updateSQL = $DB->updateData("Member_Users",
		array(
			"Password" => $secure_pass
		),
		array(
			"User_ID" => $this->user_id
		));

		return($updateSQL);
	}

	//Update a users email
	public function updateEmail($email){
		global $db,$db_table_prefix;

		$this->email = $email;
		if($this->remember_me == 1) {
			updateSessionObj();
		}

		$DB = new Data;
		$updateSQL = $DB->updateData("Member_Users",
		array(
			"Email" => $email
		),
		array(
			"User_ID" => $this->user_id
		));

		return($updateSQL);
	}

	//Fetch all user group information
	public function groupID(){
		global $pdo_db;
		$getSQL = $pdo_db->prepare("SELECT Member_Users.Group_ID, Member_Groups.* FROM Member_Users INNER JOIN Member_Groups ON Member_Users.Group_ID = Member_Groups.Group_ID WHERE User_ID = :userid");
		$getSQL->bindParam(":userid", $this->user_id);
		$getSQL->execute();
		$getSQL->setFetchMode(PDO::FETCH_ASSOC);
		$getSQL = $getSQL->fetchAll();

		return($getSQL[0]);
	}

	//Is a user member of a group
	public function isGroupMember($id){
		global $pdo_db;
		$getSQL = $pdo_db->prepare("SELECT Member_Users.Group_ID, Member_Groups.* FROM Member_Users INNER JOIN Member_Groups ON Member_Users.Group_ID = Member_Groups.Group_ID WHERE User_ID = :userid AND Member_Users.Group_ID = :groupid LIMIT 1");
		$getSQL->bindParam(":userid", $this->user_id);
		$getSQL->bindParam(":groupid", $id);
		$getSQL->execute();
		$getSQL->setFetchMode(PDO::FETCH_ASSOC);
		$getSQL = $getSQL->fetchAll();

		if(is_array($getSQL) && count($getSQL) > 0){
		    return true;
		} else {
		    return false;
		}
	}

	//Logout
	function userLogOut(){
		destorySession("Template");
	}
}
?>
