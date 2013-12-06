<?php
require_once('models/config.php');
if(!isUserLoggedIn()) { header('Location: login.php'); die(); }


if(isset($_POST["changeprofilesubmit"]))
{
	$profileerrors_first = array();
	$profileerrors_last = array();
	$profileerrors_phone = array();
	$firstname = $_POST["firstname"];
	$lastname = $_POST["lastname"];
	$phone = $_POST["phone"];
	
	if(trim($firstname) == ""){
		$profileerrors_first[] = "Please specify a first name";
	}
	if(trim($lastname) == ""){
		$profileerrors_last[] = "Please specify a last name";
	}
	if(trim($phone) == ""){
		$profileerrors_phone[] = "Please specify a phone number";
	}

	if($firstname != $loggedInUser->first_name){
		if(count($profileerrors_first) == 0){
			$loggedInUser->updateFirstName($firstname);
		}
	}

	if($lastname != $loggedInUser->last_name){
		if(count($profileerrors_last) == 0){
			$loggedInUser->updateLastName($lastname);
		}
	}

	if($phone != $loggedInUser->phone){
		if(!is_numeric($phone)){
			$profileerrors_phone[] = "Please specify a valid phone number";
		} else {
			if(count($profileerrors_phone) == 0){
				$loggedInUser->updatePhone($phone);
			}
		}
	}
	
	
}

if(isset($_POST["changeemailsubmit"]))
{
	$emailerrors = array();
	$email = $_POST["email"];
	
	if(trim($email) == "")
	{
		$emailerrors[] = lang("ACCOUNT_SPECIFY_EMAIL");
	}
	else if(!isValidEmail($email))
	{
		$emailerrors[] = lang("ACCOUNT_INVALID_EMAIL");
	}
	else if($email == $loggedInUser->email)
	{
		$emailerrors[] = lang("NOTHING_TO_UPDATE");
	}
	else if(emailExists($email))
	{
		$emailerrors[] = lang("ACCOUNT_EMAIL_TAKEN");	
	}
	
	if(count($emailerrors) == 0)
	{
		$loggedInUser->updateEmail($email);
	}
}

if(isset($_POST["changepasssubmit"])) {
	$errors = array();
	$password = $_POST["password"];
	$password_new = $_POST["passwordc"];
	$password_confirm = $_POST["passwordcheck"];
	
	if(trim($password) == "") {
		$errors[] = lang("ACCOUNT_SPECIFY_PASSWORD");
	} else if(trim($password_new) == "") {
		$errors[] = lang("ACCOUNT_SPECIFY_NEW_PASSWORD");
	} else if(minMaxRange(8,50,$password_new)) {	
		$errors[] = lang("ACCOUNT_NEW_PASSWORD_LENGTH",array(8,50));
	} else if($password_new != $password_confirm) {
		$errors[] = lang("ACCOUNT_PASS_MISMATCH");
	}

	if(count($errors) == 0) {
		//Confirm the hash's match before updating a users password
		$entered_pass = crypt($password,$loggedInUser->hash_pw);
		
		//Also prevent updating if someone attempts to update with the same password
		$entered_pass_new = crypt($password_new,$loggedInUser->hash_pw);
	
		if($entered_pass != $loggedInUser->hash_pw) {
			//No match
			$errors[] = lang("ACCOUNT_PASSWORD_INVALID");
		} else if($entered_pass_new == $loggedInUser->hash_pw) {
			//Don't update, this fool is trying to update with the same password ¬¬
			$errors[] = lang("NOTHING_TO_UPDATE");
		} else {
			//This function will create the new hash and update the hash_pw property.
			$loggedInUser->updatePassword($password_new);
		}
	}
}
?>

<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width">

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
    
    <link rel="stylesheet" href="css/external/gridiculous.css">
    <link rel="stylesheet" href="css/style.min.css">
    <link rel="stylesheet" href="css/external/font-awesome.css">
    <script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
    
    <title><?php echo $websiteName;?> - Account</title>
</head>
<body>

<?php include_once("layout_inc/header.php");?>
<div class="grid w960 container account">
    <div class="row">
        <div class="c12">
			<h1>Change your email address</h1>
			<?php
			if(isset($_POST["changeemailsubmit"]))
			{
				if(count($emailerrors) > 0)
					{
					?>
					<div id="errors">
						<?php errorBlock($emailerrors); ?>
					</div>     
					<?php } else { ?> 
					<div id="success">
						<p><?php echo lang("ACCOUNT_DETAILS_UPDATED"); ?></p>
					</div>
				<? } 
			}
			?>
			<form name="changeEmail" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" class="changeemail">
				<label for="email">Email Address:</label>
				<input type="email" name="email" value="<?php echo $loggedInUser->email; ?>" />
				<div class="clear"></div>
				<input type="submit" value="Update Email" name="changeemailsubmit" class="bluebutton1"/>
			</form>

			<h1>Change your password</h1>
			<?php
			if(isset($_POST["changepasssubmit"]))
			{
				if(count($errors) > 0)
					{ ?>
						<div id="errors">
							<?php errorBlock($errors); ?>
						</div>     
					<?php } else { ?> 
						<div id="success">
							<p><?php echo lang("ACCOUNT_DETAILS_UPDATED"); ?></p>
						</div>
			<? } }?>


			<form name="changePass" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" class="changepass">
				<label for="password">Current Password:</label>
				<input placeholder="Current password" type="password" name="password" >
				<label for="passwordc">New Password:</label>
				<input placeholder="New password" type="password" name="passwordc" >
				<label for="passwordcheck">Confirm Password:</label>
				<input placeholder="Confirm new password" type="password" name="passwordcheck" >
				<div class="clear"></div>
				<input type="submit" value="Update Password" name="changepasssubmit" class="bluebutton1">
			</form>
		</div>
	</div>
</div>

<script src="js/jquery.validate.min.js"></script>
</body>
</html>