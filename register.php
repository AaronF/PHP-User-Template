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
<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<title>Register</title>
	<meta name="description" content="">
	<meta name="author" content="">

	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<link rel="stylesheet" href="stylesheets/style.css">

	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<link rel="shortcut icon" href="images/favicon.ico">
	<link rel="apple-touch-icon" href="images/apple-touch-icon.png">
	<link rel="apple-touch-icon" sizes="72x72" href="images/apple-touch-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="114x114" href="images/apple-touch-icon-114x114.png">

</head>
<body>
	<div class="grid w960">
		<div class="row">
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
</body>
</html>
