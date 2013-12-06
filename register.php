<?php
	require_once("models/config.php");
	if(isUserLoggedIn()) { header("Location: dashboard.php"); die(); }

	//sign up
	if(!empty($_POST)){
		$errors = array();
		$email = trim($_POST["email"]);
		// REMOVED $username = trim($_POST["username"]);
		$password = trim($_POST["password"]);
		$confirm_pass = trim($_POST["passwordc"]);
		$unique = rand_string(10);

		if(minMaxRange(8,50,$password) && minMaxRange(8,50,$confirm_pass)) {
			$errors[] = lang("ACCOUNT_PASS_CHAR_LIMIT",array(8,50));
		} else if($password != $confirm_pass) {
			$errors[] = lang("ACCOUNT_PASS_MISMATCH");
		}
		if(!isValidEmail($email)) {
			$errors[] = lang("ACCOUNT_INVALID_EMAIL");
		}

		if(count($errors) == 0){	
			$user = new User($password,$email);
			
			if(!$user->status) {
				// if($user->username_taken) $errors[] = lang("ACCOUNT_USERNAME_IN_USE",array($username));
				if($user->email_taken) {
					$errors[] = lang("ACCOUNT_EMAIL_IN_USE",array($email));
				} 	  	
				
			} else {
				if(!$user->userCakeAddUser()) {
					if($user->mail_failure) $errors[] = lang("MAIL_ERROR");
					if($user->sql_failure)  $errors[] = lang("SQL_ERROR");
				}
			}
			header("Location: login.php?m=1");
			
		}
	}
?>
<!doctype html>
<html lang="en" class="login_page">
<head>
	<meta charset="utf-8" />

	<meta name="viewport" content="width=device-width, minimum-scale=1.0"> 
	<link rel="shortcut icon" href="../siteimages/favicon.ico">
	<meta name="description" content=""/>
	<meta name="keywords" content="">
	<!--[if lte IE 9]><link rel="stylesheet" href="css/ie.css" type="text/css" media="screen" /><![endif]-->

    <link rel="stylesheet" href="css/external/gridiculous.css">
    <link rel="stylesheet" href="css/style.min.css">
    <link rel="stylesheet" href="css/external/font-awesome.css">

    <script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
    
    <title><?php echo $websiteName;?> - Sign up</title>
</head>
<body>
<?php include_once("layout_inc/header.php");?>
<div class="grid w800">
	<div class="row clear">
		<div class="c4"></div>
		<div class="c4 register">
			<form class="signupform" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
				<h2>Sign Up</h2>
		
		        <div class="errormsg hide"></div>
		        <div class="successmsg hide"></div>

				<input type="email" name="email" placeholder="Email address" required="yes">
				<input type="password" name="password" placeholder="Password" required="yes">
				<input type="password" name="passwordc" id="passwordc" placeholder="Confirm password" required="yes">

				<input type="submit" name="submit" value="Sign Up">
			</form>
		</div>
		<div class="c4 end"></div>
	</div>
</div>
<script src="js/jquery.validate.min.js"></script>
</body>
</html>