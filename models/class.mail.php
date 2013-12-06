<?php
class userCakeMail {

	public $contents = NULL;

	public function newTemplateMsg($template,$additionalHooks)
	{
		global $mail_templates_dir,$debug_mode;
	
		$this->contents = file_get_contents($mail_templates_dir.$template);

		if(!$this->contents || empty($this->contents))
		{
			if($debug_mode)
			{
				if(!$this->contents)
				{ 
					echo lang("MAIL_TEMPLATE_DIRECTORY_ERROR",array(getenv("DOCUMENT_ROOT")));
							
					die(); 
				}
				else if(empty($this->contents))
				{
					echo lang("MAIL_TEMPLATE_FILE_EMPTY"); 
					
					die();
				}
			}
		
			return false;
		}
		else
		{
			$this->contents = replaceDefaultHook($this->contents);
		
			$this->contents = str_replace($additionalHooks["searchStrs"],$additionalHooks["subjectStrs"],$this->contents);

			if(strpos($this->contents,"#INC-FOOTER#") !== FALSE)
			{
				$footer = file_get_contents($mail_templates_dir."email-footer.txt");
				if($footer && !empty($footer)) $this->contents .= replaceDefaultHook($footer); 
				$this->contents = str_replace("#INC-FOOTER#","",$this->contents);
			}
			
			return true;
		}
	}
	
	public function sendMail($email,$subject,$msg = NULL)
	{
		global $websiteName,$emailAddress;
		
		$header = "MIME-Version: 1.0\r\n";
		$header .= "Content-type: text/plain; charset=iso-8859-1\r\n";
		$header .= "From: ". $websiteName . " <" . $emailAddress . ">\r\n";
		
		if($msg == NULL)
			$msg = $this->contents; 

		$message .= $msg;

		$message = wordwrap($message, 70);
			
		return mail($email,$subject,$message,$header);
	}
}


?>