<?php
	$dbtype = "mysql";
	$db_host = "localhost";
	$db_user = "TemplateSQL";
	$db_pass = "testpassword";
	$db_name = "TemplateSQL";
	$db_port = "";
	$db_table_prefix = "Member_";

	$langauge = "en";

	$websiteName = "Template";
	$websiteUrl = "http://" . $_SERVER['HTTP_HOST'] ."/";

	$emailActivation = false;

	$resend_activation_threshold = 1;

	$emailAddress = "test@test.com";

	$emailDate = date("l");

	$mail_templates_dir = "models/mail-templates/";

	$default_hooks = array("#WEBSITENAME#","#WEBSITEURL#","#DATE#");
	$default_replace = array($websiteName,$websiteUrl,$emailDate);

	$debug_mode = false;

	$remember_me_length = "2wk";

?>
