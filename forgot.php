<?php

require_once( 'core.php' );

// If the user is already logged in.
if ( $user->logged_in() ) redirect( '/' );

show_header( 'Forgot Password', FALSE, 'boxed' );

// Default POST variables.
$form_email = isset( $_POST['email'] ) ? $_POST['email'] : '';

// Form error messages.
$error->add( 'INPUT_MISSING', 'You must enter your e-mail.', 'error', 'times' );
$error->add( 'INVALID_EMAIL', 'The e-mail isn\'t in valid email format.', 'error', 'times' );
$error->add( 'INCORRECT_EMAIL', 'E-mail doesn\'t exist in the database.', 'error', 'times' );
$error->add( 'SUSPENDED', 'Your account is currently suspended.', 'error', 'times' );
$error->add( 'EXCEEDED', 'You\'ve made too many reset requests within the past day.', 'warning', 'exclamation-triangle' );

$error->add( 'SUCCESS', 'An email was sent to you containing a password reset link. Emails might take a few minutes to send.', 'success', 'check' );

if ( isset( $_GET['success'] ) ) $error->force( 'SUCCESS' );

// If login form is submitted.
if ( !empty( $_POST ) ) {
	
	$error->reset();
	
	// Check if email is missing.
	if ( empty( $_POST['email'] ) ) $error->set( 'INPUT_MISSING' );
	else {
		
		// Check if email input is vaild email format.
		if ( !is_email( $_POST['email'] ) ) $error->set( 'INVALID_EMAIL' );
		else {
			
			$email = $db->escape( $_POST['email'] );
			
			// Check if email exists in the database.
			if ( !$user->check_email( $email ) ) $error->set( 'INCORRECT_EMAIL' );
			else {
				
				$db_user = $db->select( 'id,username' )->from( 'users' )->where( array( 'email' => $email ) )->fetch()[0];
				
				// Check if user is suspended.
				if ( $user->suspended( $db_user['id'] ) ) $error->set( 'SUSPENDED' );
				else {
					
					$ip = $_SERVER['REMOTE_ADDR'];
					$today = date( 'Y-m-d' );
					
					// Grab resets from database. Max 6.
					$resets = $db->select( 'id' )->from( 'resets' )->limit(5)->where( '`requested_ip` = "'.$ip.'" AND DATE(`requested_time`) = "'.$today.'"' )->fetch();
					
					// Only allow maximum 5 requests per day (per IP address).
					if ( $db->affected_rows > 4 ) $error->set( 'EXCEEDED' );
					else {
						
						/** RESET SUCCESS **/
						
						$code = random_str( 15 );
						
						$insert = array( 'code' => $code, 'user_id' => $db_user['id'], 'requested_ip' => $ip, 'requested_time' => date( 'Y-m-d H:i:s' ) );
						
						// Insert into database.
						$db->insert( 'resets', $insert );
						
						// Send email to user.
						$email_content = "<p>Sorry to hear you forgot your password! You can reset your password by clicking on <a href=\"".MAINURL."reset?code=".$code."\">this link</a>. This password reset request expires in 48 hours.</p>\n";
						$email_content .= "<p>If you didn't request a password change, please ignore this email. No changes will be made to your account.</p>\n";
						$email_content .= "<p>Have a great day!</p><p>- MCPE Hub Team</p>\n";
						$email_content .= "<p><i>Follow us on Twitter! <a href='http://twitter.com/MCPEHubNetwork'>@MCPEHubNetwork</a> for news, updates and more!</i></p>\n";
						$email_content .= "<div class=\"bottom\">If you have issues opening the link above, use the following link: <a href=\"".MAINURL."reset?code=".$code."\">".MAINURL."reset?code=".$code."</a></div>\n";
						
						// Grab our HTML email template.
						ob_start();
						require( ABSPATH . 'core/templates/email-user.php' );
						$message = ob_get_clean();
						
						send_email( $email, $db_user['username'], 'Reset Your Password', $message );
						
						// Redirect user to success page. We redirect because we don't
						// want them to spam refresh the page (email spam).
						redirect( '/forgot?success' );
						
					} // END: Check number of requests made today.
					
				} // END: Check if user suspended.
				
			} // END: Check if email in database.
			
		} // END: Check if valid email format.
		
	} // END: Input not empty.
	
} // END: Form submitted.

?>

<script>window.onload = function() { document.getElementById('email').focus(); };</script>

<h1>Reset Password</h1>
<?php $error->display(); ?>

<?php if ( !isset( $_GET['success'] ) ) { ?>
<form action="/forgot" method="POST" class="form">
    
    <div class="group">
        <label for="email">Your Email</label>
        <input type="text" name="email" id="email" class="text" value="<?php echo htmlspecialchars($form_email); ?>" autocomplete="off" spellcheck="false" />
    </div>
    
    <button type="submit" id="submit">Reset Password</button>

</form>
<?php } ?>

<div class="links"><a href="/login">&laquo; Back to Login</a></div>

<?php show_footer(); ?>