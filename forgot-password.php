<?php
require_once("models/config.php");
if(isUserLoggedIn()) { header("Location: dashboard.php"); die(); }

$errors = array();
$success_message = "";

if(!empty($_GET["confirm"])) {
	$token = trim($_GET["confirm"]);

	if($token == "" || !validateActivationToken($token,TRUE)) {
		$errors[] = lang("FORGOTPASS_INVALID_TOKEN");
	} else {
		$rand_pass = getUniqueCode(15);
		$secure_pass = generateHash($rand_pass);

		$userdetails = fetchUserDetailsWithEmail(NULL,$token);

		$mail = new userCakeMail();

		$hooks = array(
				"searchStrs" => array("#GENERATED-PASS#","#USERNAME#"),
				"subjectStrs" => array($rand_pass,$userdetails["Email"])
		);

		if(!$mail->newTemplateMsg("your-lost-password.txt",$hooks)) {
			$errors[] = lang("MAIL_TEMPLATE_BUILD_ERROR");
		} else {
			if(!$mail->sendMail($userdetails["Email"],"Your new password")) {
					$errors[] = lang("MAIL_ERROR");
			} else {
				if(!updatePasswordFromToken($secure_pass,$token)) {
					$errors[] = lang("SQL_ERROR");
				} else {
					flagLostPasswordRequest($userdetails["Email"],0);
					$success_message  = lang("FORGOTPASS_NEW_PASS_EMAIL");
				}
			}
		}

	}
}

if(!empty($_GET["deny"])) {
	$token = trim($_GET["deny"]);

	if($token == "" || !validateActivationToken($token,TRUE)) {
		$errors[] = lang("FORGOTPASS_INVALID_TOKEN");
	} else {
		$userdetails = fetchUserDetailsWithEmail(NULL,$token);

		flagLostPasswordRequest($userdetails['Email'],0);

		$success_message = lang("FORGOTPASS_REQUEST_CANNED");
	}
}

if(!empty($_POST)) {
	$email = $_POST["email"];
	// $username = $_POST["username"];


	if(trim($email) == "") {
		$errors[] = lang("ACCOUNT_SPECIFY_EMAIL");
	} else if(!isValidEmail($email) || !emailExists($email)) {
		$errors[] = lang("ACCOUNT_INVALID_EMAIL");
	}

	if(count($errors) == 0) {
		$userdetails = fetchUserDetailswithEmail($email);

		if($userdetails["LostPasswordRequest"] == 1) {
			$errors[] = lang("FORGOTPASS_REQUEST_EXISTS");
		} else {
			$mail = new userCakeMail();

			$confirm_url = lang("CONFIRM")."\n".$websiteUrl."forgot-password.php?confirm=".$userdetails["ActivationToken"];
			$deny_url = ("DENY")."\n".$websiteUrl."forgot-password.php?deny=".$userdetails["ActivationToken"];

			$hooks = array(
				"searchStrs" => array("#CONFIRM-URL#","#DENY-URL#","#USERNAME#"),
				"subjectStrs" => array($confirm_url,$deny_url,$userdetails["Email"])
			);

			if(!$mail->newTemplateMsg("lost-password-request.txt",$hooks)) {
				$errors[] = lang("MAIL_TEMPLATE_BUILD_ERROR");
			} else {
				if(!$mail->sendMail($userdetails["Email"],"Lost password request")) {
					$errors[] = lang("MAIL_ERROR");
				} else {
					flagLostPasswordRequest($email,1);

					$success_message = lang("FORGOTPASS_REQUEST_SUCCESS");
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
	<title>Forgot Password</title>
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
			<div class="col-md-6 col-md-offset-3">
				<form class="signupform" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
					<h2>Forgot Password</h2>
					<?php
			        if(!empty($_POST) || !empty($_GET)) {
			            if(count($errors) > 0) {
						?>
				        	<div class="errormsg">
				            	<?php errorBlock($errors); ?>
				            </div>
			        	<?
			            } else {
						?>
				            <div class="successmsg">
				                <p><?php echo $success_message; ?></p>
							</div>
			        	<?
						}
			        }
			        ?>
					<div class="form-group">
						<label for="email">Email:</label>
						<input type="email" name="email" placeholder="Email address" class="form-control">
					</div>
					<button id="loginbutton" class="btn btn-primary right submit" type="submit" value="Reset"><i class="icon-ok"></i> Reset</button>
				</form>
			</div>
		</div>
	</div>
</body>
</html>
