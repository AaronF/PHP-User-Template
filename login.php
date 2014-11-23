<?php
	require_once("models/config.php");
	if(isUserLoggedIn()) { header("Location: index.php"); die(); }

	//login
	if(!empty($_POST)){
		$errors = array();
		$email = trim($_POST["email"]);
		$password = trim($_POST["password"]);
		$remember_choice = trim($_POST["remember_me"]);

		if($email == ""){
			$errors[] = lang("ACCOUNT_SPECIFY_USERNAME");
		}
		if($password == ""){
			$errors[] = lang("ACCOUNT_SPECIFY_PASSWORD");
		}

		if(count($errors) == 0){
			if(!emailExists($email)){
				$errors[] = lang("ACCOUNT_USER_OR_PASS_INVALID");
			} else {
				$userdetails = fetchUserDetailswithEmail($email);

				if($userdetails["Active"]==0) {
					$errors[] = lang("ACCOUNT_INACTIVE");
				} else {
					if (crypt($password, $userdetails["Password"]) != $userdetails["Password"]) {
						$errors[] = lang("ACCOUNT_USER_OR_PASS_INVALID");
					} else {
						$loggedInUser = new loggedInUser();
						$loggedInUser->email = $userdetails["Email"];
						$loggedInUser->user_id = $userdetails["User_ID"];
						$loggedInUser->hash_pw = $userdetails["Password"];
						// $loggedInUser->display_username = $userdetails["Username"];
						// $loggedInUser->clean_username = $userdetails["Username_Clean"];
						$loggedInUser->remember_me = $remember_choice;
						$loggedInUser->remember_me_sessid = rand_string( 15 );

						$loggedInUser->updateLastSignIn();

						if($loggedInUser->remember_me == 0) {
							$_SESSION["Template"] = $loggedInUser;
						} else if($loggedInUser->remember_me == 1) {
							// $_SESSION["farelert"] = $loggedInUser;
							$db->sql_query("INSERT INTO ".$db_table_prefix."Sessions VALUES('".time()."', '".serialize($loggedInUser)."', '".$loggedInUser->remember_me_sessid."')");
							setcookie("TemplateUser", $loggedInUser->remember_me_sessid, time()+parseLength($remember_me_length));
						}

						header("Location: account.php");
						die();
					}
				}
			}
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
	<title>Login</title>
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
			<div class="c4 login">
				<form class="signupform" name="login_form" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
					<h2>Login</h2>

					<div class="errormsg hide">
						<p></p>
					</div>

					<input placeholder="Email" type="text" name="email" />
	                <input placeholder="Password" type="password" name="password" />
	                <label for="remember_me" class="float--left">Remember Me?</label>
	                <input type="checkbox" name="remember_me" value="1" id="checkbox" />

	                <input type="submit" name="submit" value="Login">

	                <p><a href="forgot-password.php">Forgot Password?</a></p>
	                <div class="cf"></div>
				</form>
			</div>
			<div class="c4 end"></div>
		</div>
	</div>

</body>
</html>
