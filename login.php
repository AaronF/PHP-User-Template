<?php
	require_once("models/config.php");
	if(isUserLoggedIn()) { header("Location: index.php"); die(); }

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
							$DB = new Data;
							$insertSQL = $DB->insertData("Member_Sessions", array("sessionStart" => time(), "sessionData" => serialize($loggedInUser), "sessionID" => $loggedInUser->remember_me_sessid));

							setcookie("Template", $loggedInUser->remember_me_sessid, time()+parseLength($remember_me_length));
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

	<!-- Mobile Specific Metas -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<!-- CSS -->
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/style.css">
	<link rel="stylesheet" href="assets/css/font-awesome.min.css">

	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<!-- Favicons -->
	<link rel="shortcut icon" href="assets/images/favicon.ico">
	<link rel="apple-touch-icon" href="assets/images/apple-touch-icon.png">
	<link rel="apple-touch-icon" sizes="72x72" href="assets/images/apple-touch-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="114x114" href="assets/images/apple-touch-icon-114x114.png">
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="col-md-3"></div>
			<div class="col-md-6">
				<form class="signupform" name="login_form" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
					<h2>Login</h2>

					<div class="errormsg hide">
						<p></p>
					</div>

					<div class="form-group">
						<label for="emailinput">Email:</label>
						<input type="email" class="form-control" id="emailinput" name="email" placeholder="Email">
					</div>
					<div class="form-group">
						<label for="passwordinput">Password</label>
    					<input type="password" class="form-control" id="passwordinput" name="password" placeholder="Password">
					</div>
					<div class="checkbox">
						<label>
							<input type="checkbox" name="remember_me" value="1"> Remember Me
						</label>
					</div>

	                <input type="submit" name="submit" class="btn btn-primary right" value="Login">

	                <p><a href="forgot-password.php">Forgot Password?</a></p>
				</form>
			</div>
			<div class="col-md-3"></div>
		</div>
	</div>
</body>
</html>
