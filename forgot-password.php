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
				"subjectStrs" => array($rand_pass,$userdetails["Username"])
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
	$username = $_POST["username"];
	
	
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
				"subjectStrs" => array($confirm_url,$deny_url,$userdetails["Username"])
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
    <script src="js/vendor/modernizr-2.6.2.min.js"></script>
    <script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>

    <title><?php echo $websiteName;?> - Password Reset</title>
</head>
<body>

<?php include_once("layout_inc/header.php");?>

<div class="grid w800">
	<div class="row cf">
		<div class="c4"></div>
		<div class="c4 login">
		<form class="signupform" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
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
			<input type="email" name="email" placeholder="Email address">
			<button id="loginbutton" class="bluebutton1 submit" type="submit" value="Reset"><i class="icon-ok"></i> Reset</button>
		</form>
	</div>
</div>
</body>
</html>