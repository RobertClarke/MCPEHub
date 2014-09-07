<?php

require_once( 'core.php' );

// If the user is already logged in.
if ( $user->logged_in() ) {
	
	// Log them out or redirect to the homepage.
	if ( isset( $_GET['logout'] ) ) $user->log_out();
	else redirect( '/' );
	
}

show_header( 'Sign In', FALSE, 'boxed' );

// Default GET variables.
$redirect = isset( $_GET['redirect'] ) ? $_GET['redirect'] : '';

// Default POST variables.
$form_username = isset( $_POST['username'] ) ? $_POST['username'] : '';

// Adding all error messages we might need.
$error->add( 'LOGGED_OUT', 'You have been securely logged out.', 'success', 'lock' );
$error->add( 'AUTH_REQ', 'You must sign in to continue.', 'warning', 'lock' );
$error->add( 'REGISTERED', '<strong>Welcome to MCPE Hub!</strong><br />You may now sign in. Please check your email inbox for an activation link.', 'success', 'smile-o' );

// Form error messages.
$error->add( 'INPUT_MISSING', 'Both username &amp; password required.', 'error', 'times' );
$error->add( 'INVALID_USER', 'That username doesn\'t exist.', 'error', 'times' );
$error->add( 'INVALID_PASS', 'Incorrect username / password combo.', 'error', 'times' );
$error->add( 'SUSPENDED', 'Your account is currently suspended.', 'error', 'times' );

// Force errors if user logged out or auth is requested.
if ( isset( $_GET['logged_out'] ) ) $error->force( 'LOGGED_OUT' );
else if ( isset( $_GET['auth_req'] ) ) $error->force( 'AUTH_REQ' );
else if ( isset( $_GET['registered'] ) ) $error->force( 'REGISTERED' );

// If login form is submitted.
if ( !empty( $_POST ) ) {
	
	$error->reset();
	$remember = isset( $_POST['remember'] ) ? TRUE : FALSE;
	
	// Check if username or password are missing.
	if ( empty( $_POST['username'] ) || empty( $_POST['password'] ) ) $error->set( 'INPUT_MISSING' );
	else {
		
		$username = $db->escape( $_POST['username'] );
		$password = $db->escape( $_POST['password'] );
		
		// Check if username exists in the database.
		if ( !$user->check_username( $username ) ) $error->set( 'INVALID_USER' );
		else {
			
			$db_hash = $user->info( 'password', $username );
			
			// Verify the password with the database.
			if ( !password_verify( $password, $db_hash ) ) $error->set( 'INVALID_PASS' );
			else {
				
				// Check if user is suspended.
				if ( $user->suspended( $username ) ) $error->set( 'SUSPENDED' );
				else {
					
					/** LOGIN SUCCESS **/
					
					// Set a cookie.
					$user->set_auth_cookie( $username, $remember );
					
					// Update some database values for user.
					$update = array( 'last_ip' => $_SERVER['REMOTE_ADDR'], 'last_login' => date( 'Y-m-d H:i:s' ) );
					$db->where( array( 'username' => $username ) )->update( 'users', $update );
					
					// Redirect user, as needed.
					if ( !empty( $_POST['redirect'] ) ) redirect( $_POST['redirect'] );
					else redirect( '/dashboard?logged_in' );
					
				} // END: User not suspended.
				
			} // END: Password verified.
			
		} // END: Username exists.
		
	} // END: Inputs not empty.
	
} // END: Form submitted.

?>

<script>window.onload = function() { document.getElementById('<?php echo ( empty( $form_username ) ) ? 'username' : 'password'; ?>').focus(); };</script>

<h1>Sign In</h1>
<?php $error->display(); ?>

<form action="/login" method="POST" class="form">
    
    <div class="group">
        <label for="username">Username</label>
        <input type="text" name="username" id="username" class="text" value="<?php echo htmlspecialchars($form_username); ?>" spellcheck="false" />
    </div>
    
    <div class="group">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" class="text" value="" />
    </div>
    
    <button type="submit" id="submit" class="plus-remember">Sign In</button>
    
    <div class="remember">
        <input type="checkbox" name="remember" id="remember"<?php if ( isset( $remember ) && $remember ) echo ' checked'; ?> /> <label for="remember">Remember Me</label>
    </div>
    
    <input type="hidden" name="redirect" value="<?php echo ( empty( $_POST ) ) ? htmlspecialchars( $redirect ) : htmlspecialchars( $_POST['redirect'] ); ?>" />

</form>

<div class="links"><strong><a href="/register">Create an Account</a></strong> <a href="/forgot">Forgot Password?</a></div>

<?php show_footer(); ?>