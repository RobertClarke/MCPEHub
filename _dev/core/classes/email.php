<?php

/**
  
  * Email Class
  *
  * This class simplifies sending emails by removing additional
  * steps such as including PHPMailer multiple times, etc.
  *
  * format();	Format email with template.
  * send();		Send email with given variables through SMTP server.
  * init();		Initialize PHPMailer class, when needed.
  
**/

class Email {
	
	private $init = FALSE;
	private $PHPMailer;
	
	// Format email with template.
	public function format($content, $title='', $user='') {
		
		// Have to do this to pass variables onto template.
		global $email_content, $email_title, $email_user;
		
		$email_content = $content;
		$email_title = $title;
		$email_user = $user;
		
		ob_start();
		include( ABS . 'core/templates/email-user.php' );
		$message = ob_get_clean();
		
		return $message;
		
	}
	
	// Send email with given variables through SMTP server.
	public function send($email, $name, $subject, $content) {
		
		// Initialize PHPMailer.
		$this->init();
		
		global $PHPMailer;
		
		$PHPMailer->addAddress($email, $name);
		$PHPMailer->Subject = $subject;
		$PHPMailer->Body = $content;
		
		$PHPMailer->send();
		
		return;
		
	}
	
	// Initialize PHPMailer class, when needed.
	public function init() {
		
		// If PHPMailer already initialized, don't include it again.
		if ( !$this->init ) {
			$this->init = TRUE;
			require_once( ABS . 'core/phpmailer/phpmailer_autoload.php' );
		}
		
		global $PHPMailer;
		
		$PHPMailer = new PHPMailer();
		
		// SMTP Settings.
		$PHPMailer->isSMTP();
		$PHPMailer->SMTPDebug = 0;
		$PHPMailer->SMTPAuth = TRUE;
		$PHPMailer->SMTPSecure = 'tls';
		
		// Authentication settings.
		$PHPMailer->Host = MAIL_HOST;
		$PHPMailer->Port = MAIL_PORT;
		$PHPMailer->Username = MAIL_USER;
		$PHPMailer->Password = MAIL_PASS;
		
		// Set sender.
		$PHPMailer->setFrom('noreply@mcpehub.com', 'MCPE Hub');
		
		$PHPMailer->isHTML(TRUE);
		
		return;
		
	}
	
}

?>