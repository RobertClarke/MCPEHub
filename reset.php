<?php

require_once( 'core.php' );

// If the user is already logged in.
if ( $user->logged_in() ) redirect( '/' );

show_header( 'Reset Password', FALSE, 'boxed' );

// Default POST variables.
if ( isset( $_GET['code'] ) ) $form_code = $_GET['code'];
else if ( isset( $_POST['code'] ) ) $form_code = $_POST['code'];
else $form_code = '';

// Form error messages.
$error->add( 'MISSING_CODE', 'URL is missing the reset token.', 'error', 'times' );
$error->add( 'INCORRECT_CODE', 'The reset token is invalid.', 'error', 'times' );

$error->add( 'INFO', 'Don\'t worry, we forget passwords too! Enter your new password below.', 'info', 'info' );
$error->add( 'INPUT_MISSING', 'You must fill out both inputs.', 'error', 'times' );
$error->add( 'PASS_MISMATCH', 'The passwords entered don\'t match.', 'error', 'times' );
$error->add( 'INVALID_PASSWORD', 'Your password must be between 6-30 characters long.', 'error', 'times' );
$error->add( 'EXPIRED', 'This reset token has expired.', 'error', 'times' );

$error->add( 'SUCCESS', 'Your password has been changed!', 'success', 'check' );

if ( isset( $_GET['success'] ) ) $error->force( 'SUCCESS' );
if ( isset( $_GET['activated'] ) ) $error->force( 'ACTIVATED' );

// If reset code is missing.
if ( empty( $form_code ) ) $error->force( 'MISSING_CODE' );
else {
	
	// Check if reset code is valid (in database).
	$db_reset = $db->select( 'id,user_id,requested_time' )->from( 'resets' )->where( array( 'code' => $form_code ) )->fetch();
	
	if ( !$db->affected_rows ) $error->set( 'INCORRECT_CODE' );
	else {
		
		$db_reset = $db_reset[0];
		
		// Set 2 day expiry time.
		$expiry = strtotime( $db_reset['requested_time'] ) + ( 60*60*24*2 );
		
		// Check if reset code has expired (48 hours).
		if( $expiry < time() ) $error->set( 'EXPIRED' );
		else {
			
			// Set variable to show form on page.
			$code_validated = TRUE;
			
			// Show a nice message to the user. We'll reset it later.
			$error->set( 'INFO' );
			
			// If reset form is submitted.
			if ( !empty( $_POST ) ) {
				
				$error->reset();
				
				// Check if inputs are missing.
				if ( empty( $_POST['password'] ) || empty( $_POST['password-repeat'] ) ) $error->set( 'INPUT_MISSING' );
				else {
					
					// Check if password between 6-30 characters.
					if ( strlen( $_POST['password'] ) < 6 || strlen( $_POST['password'] ) > 30 ) $error->append( 'INVALID_PASSWORD' );
					else {
						
						$code = $db->escape( $form_code );
						$password = $db->escape( $_POST['password'] );
						$password_repeat = $db->escape( $_POST['password-repeat'] );
						
						// Check if passwords match.
						if ( $password != $password_repeat ) $error->set( 'PASS_MISMATCH' );
						else {
							
							// Set $code_validated to false to hide the form.
							$code_validated = null;
							
							// Delete all other resets for that user. They're invalid now.
							$db->where( '`user_id`='. $db_reset['user_id'] )->delete('resets')->execute();
							
							// Hash the new password.
							$password_hash = password_hash( $password, PASSWORD_DEFAULT );
							
							// Update password in database for user.
							$db->where( '`id`=' . $db_reset['user_id'] )->update( 'users', array( 'password' => $password_hash ) );
							
							$error->set( 'SUCCESS' );
							
						} // END: Passwords match.
						
					} // END: Password between 6-30 characters.
					
				} // END: Inputs not missing.
				
			} // END: Code not expired.
			
		} // END: If form is submitted.
		
	} // END: If the reset code exists in database.
	
} // END: If reset code isn't missing from URL.

?>

<script>window.onload = function() { document.getElementById('code').focus(); };</script>

<h1>Reset Password</h1>
<?php $error->display(); ?>

<?php if ( isset( $code_validated ) ) { ?>
<form action="/reset" method="POST" class="form">
    
    <div class="group">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" class="text" value="" />
    </div>
    
    <div class="group">
        <label for="password-repeat">Repeat Password</label>
        <input type="password" name="password-repeat" id="password-repeat" class="text" value="" />
    </div>
    
    <input type="hidden" name="code" value="<?php echo htmlspecialchars($form_code); ?>" />
    
    <button type="submit" id="submit">Change Password</button>

</form>
<?php } ?>

<div class="links"><a href="/login">&laquo; Back to Login</a></div>

<?php show_footer(); ?>