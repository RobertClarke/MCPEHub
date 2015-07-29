<?php

/**
 * Email Class
 *
 * An object containing all necessary information and options
 * for sending an email to a given user in the system. Uses
 * the PHPMailer class to send messages through SMTP.
**/

class Email {

	protected $PHPMailer;

	protected $Smarty = null;
	private $template = null;

	/**
	 * Constructor
	 *
	 * @since 3.0.0
	**/
	public function __construct( $email, $subject, $name='' ) {

		// Include the PHPMailer files + classes.
		require_once( CORE . 'phpmailer/phpmailer.php' );
		$PHPMailer = new PHPMailer();

		// Configure SMTP settings.
		$PHPMailer->isSMTP();
		$PHPMailer->SMTPDebug = 0;
		$PHPMailer->SMTPAuth = true;
		$PHPMailer->SMTPSecure = 'tls';

		// Mail server settings.
		$PHPMailer->Host = MAIL_HOST;
		$PHPMailer->Port = MAIL_PORT;
		$PHPMailer->Username = MAIL_USER;
		$PHPMailer->Password = MAIL_PASS;

		$PHPMailer->Subject = $subject;
		$PHPMailer->isHTML(true);

		// Sender settings.
		$PHPMailer->setFrom('noreply@mcpehub.com', 'MCPE Hub');

		// Recipient settings.
		$PHPMailer->addAddress($email, $name);

		$this->PHPMailer = $PHPMailer;

	}




	public function send() {

		// Check for Smarty template.
		if ( $this->Smarty !== null && $this->template !== null ) {
			$this->Smarty->assign('title', $this->PHPMailer->Subject);
			$this->Smarty->setTemplateDir(CORE.'templates/emails/');
			$this->PHPMailer->Body = $this->Smarty->fetch($this->template.'.tpl');
		}

		if ( empty( $this->PHPMailer->Body ) )
			return false;

		$this->PHPMailer->send();

	}

	public function set_content( $content ) {
		$this->PHPMailer->Body = $content;
		return $this;
	}






	public function set_template( $tpl ) {

		if ( file_exists(CORE.'templates/emails/'.$tpl.'.tpl') )
			$this->template = $tpl;

		return $this;

	}

	public function add_smarty( $smarty ) {

		if ( $smarty instanceof Smarty )
			$this->Smarty = $smarty;

		return $this;

	}

}

?>