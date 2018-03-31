<?php
	require_once("models/config.php");
	if(isUserLoggedIn()) { header("Location: account.php"); die(); }

	//login
	if(!empty($_POST)){
		$errors = array();
		$email = trim($_POST["email"]);
		$password = trim($_POST["password"]);
		if(isset($_POST["remember_me"])){
			$remember_choice = trim($_POST["remember_me"]);
		} else {
			$remember_choice = 0;
		}

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
					if(password_verify($password, $userdetails["Password"])) {
	    				// Authenticated

						//Has the hashing algorithm changed since the user last login?
						//If so, rehash the users password
	    				if(password_needs_rehash($userdetails["Password"], PASSWORD_DEFAULT)) {
							$hash = password_hash($password, PASSWORD_DEFAULT);
							$Data = new Data;
							$updateHash = $Data->updateData(
								"Member_Users",
								array("Password" => $hash),
								array("User_ID" => $userdetails["User_ID"])
							);
						}

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
							$DB = new Data;
							$insertSQL = $DB->insertData("Member_Sessions", array("sessionStart" => time(), "sessionData" => serialize($loggedInUser), "sessionID" => $loggedInUser->remember_me_sessid));

							setcookie("Template", $loggedInUser->remember_me_sessid, time()+parseLength($remember_me_length));
						}

						header("Location: account.php");
						die();
					} else {
						$errors[] = lang("ACCOUNT_USER_OR_PASS_INVALID");
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

	<!-- Mobile Specific Metas -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
</head>
<body>
	<h2>Login</h2>
	<form name="login_form" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
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

		<div class="checkbox">
			<label>
				<input type="checkbox" name="remember_me" value="1"> Remember Me
			</label>
		</div>

        <input type="submit" name="submit" value="Login">

        <p><a href="forgot-password.php">Forgot Password?</a></p>
	</form>
</body>
</html>
