<?php

require_once( 'core.php' );

// If the user is already logged in.
if ( $user->logged_in() ) redirect( '/' );

show_header( 'Register', FALSE, 'boxed' );

// Default POST variables.
$form_username = isset( $_POST['username'] ) ? $_POST['username'] : '';
$form_email = isset( $_POST['email'] ) ? $_POST['email'] : '';

// Adding all error messages we might need.
$error->add( 'LOGGED_OUT', 'You have been logged out.', 'success', 'check' );
$error->add( 'AUTH_REQ', 'You must sign in to continue.', 'warning', 'exclamation-triangle' );

// Form error messages.
$error->add( 'INPUTS_MISSING', 'You must fill in all the inputs.', 'error', 'times' );
$error->add( 'INVALID_LOGIN', 'Incorrect username/password combination.', 'error', 'times' );
$error->add( 'SUSPENDED', 'Your account is currently suspended.', 'error', 'times' );

$error->add( 'INVALID_EMAIL', 'The e-mail isn\'t in valid email format.', 'error', 'times' );
$error->add( 'USED_EMAIL', 'That e-mail is already in use by another user.', 'error', 'times' );
$error->add( 'INVALID_USERNAME', 'Username must be 3-20 characters long, contain "A-Z, 0-9, _" only.', 'error', 'times' );
$error->add( 'USED_USERNAME', 'That username is already in use by another user.', 'error', 'times' );
$error->add( 'LONG_EMAIL', 'E-mail must be max 50 characters.', 'error', 'times' );

$error->add( 'TOS_MISSING', 'You must accept the terms of use.', 'error', 'times' );

$error->add( 'PASS_MISMATCH', 'The passwords entered don\'t match.', 'error', 'times' );
$error->add( 'INVALID_PASSWORD', 'Your password must be between 6-30 characters long.', 'error', 'times' );

// Force errors if user logged out or auth is requested.
if ( isset( $_GET['logged_out'] ) ) $error->force( 'LOGGED_OUT' );
if ( isset( $_GET['auth_req'] ) ) $error->force( 'AUTH_REQ' );

// If login form is submitted.
if ( !empty( $_POST ) ) {
	
	$error->reset();
	$accept_tos = isset( $_POST['accept_tos'] ) ? TRUE : FALSE;
	
	
	// Missing inputs check.
	if ( empty( $_POST['username'] ) || empty( $_POST['email'] ) || empty( $_POST['password'] ) || empty( $_POST['password-repeat'] ) ) $error->append( 'INPUTS_MISSING' );
	
	// Username checks.
	if ( !empty( $_POST['username'] ) ) {
		
		// Check if username between 3-20 characters.
		if ( strlen( $_POST['username'] ) < 3 || strlen( $_POST['username'] ) > 20 ) $error->append( 'INVALID_USERNAME' );
		
		// Check if username is in valid format.
		else if ( !preg_match( '/^[A-Za-z][A-Za-z0-9]*(?:_[A-Za-z0-9]+)*$/', $_POST['username'] ) ) $error->append( 'INVALID_USERNAME' );
		
		// Check if username is in use.
		else if ( $user->check_username( $db->escape( $_POST['username'] ) ) ) $error->append( 'USED_USERNAME' );
		
	}
	
	// Email checks.
	if ( !empty( $_POST['email'] ) ) {
		
		// Check if email under 50 characters.
		if ( strlen( $_POST['email'] ) > 50 ) $error->append( 'LONG_EMAIL' );
		
		// Check if email is in valid format.
		if ( !is_email( $_POST['email'] ) ) $error->append( 'INVALID_EMAIL' );
		
		// Check if email is in use.
		else if ( $user->check_email( $db->escape( $_POST['email'] ) ) ) $error->append( 'USED_EMAIL' );
		
	}
	
	// Password checks.
	if ( !empty( $_POST['password'] ) ) {
		
		// Check if password between 6-30 characters.
		if ( strlen( $_POST['password'] ) < 6 || strlen( $_POST['password'] ) > 30 ) $error->append( 'INVALID_PASSWORD' );
		
		// Check if passwords match.
		else if ( $_POST['password'] != $_POST['password-repeat'] ) $error->append( 'PASS_MISMATCH' );
		
	}
	
	// Show an error if TOS hasn't been accepted by user.
	if ( !$accept_tos ) $error->append( 'TOS_MISSING' );
	
	
	// If we have no errors in the form, lets continue.
	if ( empty( $error->selected ) ) {
		
		$username = $db->escape( $_POST['username'] );
		$email = $db->escape( $_POST['email'] );
		$password = $db->escape( $_POST['password'] );
		
		// Select a random default avatar.
		$avatars = array( 'cow', 'creeper', 'pig', 'skeleton', 'zombie', 'zombiepig' );
		$avatar = 'default_'.$avatars[rand( 0, 5 )].'.png';
		
		$code = random_str( 15 );
		
		$insert = array(
			'username'		=> $username,
			'password'		=> password_hash( $password, PASSWORD_DEFAULT ),
			'email'			=> $email,
			'joined'		=> date( 'Y-m-d H:i:s' ),
			'last_ip'		=> $_SERVER['REMOTE_ADDR'],
			'activate_code'	=> $code,
			'avatar_file'	=> $avatar,
			'suspended'		=> 0,
			'last_active'	=> 0,
			'last_login'	=> 0,
			'activated'		=> 0
		);
		
		// Insert user into database.
		$user_id = $db->insert( 'users', $insert );
		
		// Send email to user.
		$email_content = "<p>Welcome to MCPE Hub! We can't wait to see what you have to contribute to our growing community, but first we need to activate your account.</p>\n";
		$email_content .= "<p>Activate your account by clicking on <a href=\"".MAINURL."activate?code=".$code."\">this link</a>.</p>\n";
		$email_content .= "<p>Once you've activated your account, you'll be able to publish content, comment and participate in our growing community.</p>\n";
		$email_content .= "<p>Thanks for joining our community!</p><p>- MCPE Hub Team</p>\n";
		$email_content .= "<p><i>Follow us on Twitter! <a href='http://twitter.com/MCPEHubNetwork'>@MCPEHubNetwork</a> for news, updates and more!</i></p>\n";
		$email_content .= "<div class=\"bottom\">If you have issues opening the link above, use the following link: <a href=\"".MAINURL."activate?code=".$code."\">".MAINURL."activate?code=".$code."</a></div>\n";
		
		// Grab our HTML email template.
		ob_start();
		require( ABSPATH . 'core/templates/email-user.php' );
		$message = ob_get_clean();
		
		send_email( $email, $username, 'Activate Your Account', $message );
		
		// Redirect to login page with success message.
		redirect( '/login?registered' );
		
	} // END: No errors in form.
	
} // END: Form submitted.

?>

<?php if ( empty( $_POST ) || empty( $form_username ) ) { ?><script>window.onload = function() { document.getElementById('username').focus(); };</script><?php } ?>

<h1>Create an Account</h1>
<?php $error->display(); ?>

<form action="/register" method="POST" class="form">
    
    <div class="group">
        <label for="username">Username</label>
        <input type="text" name="username" id="username" class="text" value="<?php echo htmlspecialchars($form_username); ?>" autocomplete="off" spellcheck="false" maxlength="20" />
    </div>
    
    <div class="group">
        <label for="email">Email</label>
        <input type="text" name="email" id="email" class="text" value="<?php echo htmlspecialchars($form_email); ?>" spellcheck="false" maxlength="50" />
    </div>
    
    <div class="group">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" class="text" value="" />
    </div>
    
    <div class="group">
        <label for="password-repeat">Repeat Password</label>
        <input type="password" name="password-repeat" id="password-repeat" class="text" value="" />
    </div>
    
    <button type="submit" id="submit" class="plus-remember compact">Register</button>
    
    <div class="remember">
        <input type="checkbox" name="accept_tos" id="accept_tos"<?php if ( isset( $accept_tos ) && $accept_tos ) echo ' checked'; ?> /> <label for="accept_tos">I accept the <a href="/tos" target="_blank">Terms of Use</a></label>
    </div>

</form>

<div class="links"><a href="/login">&laquo; Back to Login</a></div>

<?php show_footer(); ?>