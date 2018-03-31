<?php
	require_once("models/config.php");
	if(isUserLoggedIn()) { header("Location: account.php"); die(); }

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
				if(!$user->createNewUser()) {
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

	<!-- Mobile Specific Metas -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
</head>
<body>
	<h2>Register</h2>
	<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
		<div class="errormsg">
			<?php if(count($errors) > 0){ ?>
					<?php errorBlock($errors); ?>
			<?php } ?>
		</div>

		<div>
			<label for="emailinput">Email:</label>
			<input type="email" id="emailinput" name="email" placeholder="Email">
		</div>

		<div>
			<label for="passwordinput">Password</label>
			<input type="password" id="passwordinput" name="password" placeholder="Password">
		</div>

		<div>
			<label for="passwordinput">Confirm Password</label>
			<input type="password" name="passwordc" id="passwordc" placeholder="Confirm password" required="yes">
		</div>

		<input type="submit" name="submit" value="Register">

		<p><a href="login.php">Already have an account?</a></p>
	</form>
</body>
</html>
