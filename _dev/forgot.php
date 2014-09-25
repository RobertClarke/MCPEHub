<?php

/**
  
  * Forgot Password Form
  *
  * Allows users to submit email to request a password
  * reset via email.
  
**/

require_once('core.php');

// If the user is already logged in, redirect them out of here.
if ( $user->logged_in() ) redirect('/');

show_header('Forgot Password', FALSE, ['body_id' => 'boxed']);

// Showing a message on reset success.
$error->add('SUCCESS',	'An email was sent to you containing a password reset link. Emails might take a few minutes to send.', 'success');

if ( isset( $_GET['success'] ) ) $error->force('SUCCESS');

if ( !empty( $_POST ) ) {
	
	$error->reset();
	
	$error->add('MISSING',	'You must enter your email.');
	$error->add('FORMAT',	'The email submitted isn\'t in valid email format.');
	$error->add('INVALID',	'That email isn\'t associated with an account.');
	$error->add('SUSPEND',	'Your account is currently suspended and cannot be reset.');
	$error->add('LIMIT',	'You\'ve made too many reset requests within the past day.');
	
	$f['email']	= isset( $_POST['email'] ) ? $db->escape($_POST['email']) : NULL;
	
	// Check if email missing.
	if ( empty($f['email']) ) $error->set('MISSING');
	else {
		
		// Check if email is valid in format.
		if ( !is_email($f['email']) ) $error->set('FORMAT');
		else {
			
			// Check if email exists in database.
			if ( !$user->check_email($f['email']) ) $error->set('INVALID');
			else {
				
				// Determine users id from database.
				$user_db = $db->select('id, username')->from('users')->where(['email' => $f['email']])->fetch()[0];
				
				// Check if user is suspended.
				if ( $user->suspended($user_db['id']) ) $error->set('SUSPEND');
				else {
					
					$ip = $_SERVER['REMOTE_ADDR'];
					$today = date('Y-m-d');
					
					// Figure out if clients IP has already requested over 5 times today.
					$db->select('id')->from('resets')->limit(5)->where('`request_ip`="'.$ip.'" AND DATE(`request_time`)="'.$today.'"')->fetch();
					
					// User has done over 5 requests in the day.
					if ( $db->affected_rows > 4 ) $error->set('LIMIT');
					else {
						
						/*** Reset success! :) ***/
						
						// Generate code for the reset url.
						$code = random_str(15);
						
						// Insert reset into database.
						$db->insert('resets', ['code'=>$code, 'target_user'=>$user_db['id'], 'request_ip'=>$ip, 'request_time'=>date('Y-m-d H:i:s')]);
						
						// Email HTML body.
						$email =	"<p>Sorry to hear you forgot your password! You can reset it by clicking on <a href=\"".MAINURL."reset?code={$code}\">this link</a>. This password reset request expires in 48 hours.</p>\n";
						$email .=	"<p>If you didn't request a password change, please ignore this email. No changes will be made to your account.</p>\n";
						$email .=	"<p>Have a great day!</p><p>- MCPE Hub Team</p>\n";
						$email .=	"<p><i>Follow us on Twitter: <a href=\"http://twitter.com/MCPEHubNetwork\">@MCPEHubNetwork</a> for news, updates, giveaways and more!</i></p>\n";
						$email .=	"<div class=\"bottom\">If you have issues opening the link above, use the following link: <a href=\"".MAINURL."reset?code={$code}\">".MAINURL."reset?code={$code}</a></div>\n";
						
						// Format and send email to user.
						$email = $mail->format($email);
						$email = $mail->send($f['email'], $user_db['username'], 'Reset Your Password', $email);
						
						// Redirect user to prevent spam refresh of submission.
						redirect('/forgot?success');
						
					} // End: User has done over 5 requests in the day.
					
				} // End: Check if user is suspended.
				
			} // End: Check if email exists in database.
			
		} // End: Check if email is valid in format.
		
	} // End: Check if email missing.
	
} // End: Reset form submission.

?>

<div class="title"><h2>Forgot Password</h2></div>
<div class="body">
    <?php echo $error->display(); ?>
<?php if ( !isset( $_GET['success'] ) ) { // If the form hasn't been successfully submitted. ?>
    <form action="/forgot" method="POST" class="form">
        <div class="group">
            <label for="email">Your Email</label>
            <input type="text" name="email" id="email" value="<?php $form->POST_value('email'); ?>" spellcheck="false">
        </div>
        <button type="submit" class="bttn large purple full">Reset Password</button>
    </form>
<?php } // End: If the form hasn't been successfully submitted. ?>
</div>
<div class="footer">
    <a href="/login" class="full">Back to Sign In</a>
</div>

<script>window.onload = function() { document.getElementById('email').focus(); };</script>

<?php show_footer(); ?>