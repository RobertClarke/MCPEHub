<?php

/**
  
  * Reset Password Page
  *
  * Checks the token provided by the email sent to the user
  * and verifies that the token is correct.
  
**/

require_once('core.php');

// If the user is already logged in, redirect them out of here.
if ( $user->logged_in() ) redirect('/');

show_header('Reset Password', FALSE, ['body_id' => 'boxed']);

// Setting default error messages.
$error->add('MISSING',	'The reset token is missing in the URL.');

// Use POST code over GET code, if POST exists.
if ( isset($_POST['code']) ) $f['code'] = $_POST['code'];
elseif ( isset($_GET['code']) ) $f['code'] = $_GET['code'];
else $f['code'] = NULL;

// Check if reset code is missing.
if ( empty($f['code']) ) $error->set('MISSING');
else {
	
	$error->add('INTRO',	'Don\'t worry, we forget passwords too! Enter your new password below.', 'info');
	$error->add('INVALID',	'The reset token provided has expired or doesn\'t exist.');
	$error->add('USED',		'The reset token provided has already been used.');
	
	$f['code'] = $db->escape($f['code']);
	
	$reset = $db->select('*')->from('resets')->where(['code' => $f['code']])->limit(1)->fetch();
	
	// Check if reset code exists in database.
	if ( !$db->affected_rows ) $error->set('INVALID');
	else {
		
		$reset = $reset[0];
		
		// 7 day expiry time on tokens.
		$expiry = strtotime($reset['request_time']) + (60*60*24*7);
		
		// Check if reset code has expired.
		if ( $expiry < time() || $reset['expired'] == 1 ) $error->set('INVALID');
		else {
			
			// Check if reset token has been used.
			if ( $reset['used'] == 1 ) $error->set('USED');
			else {
				
				/*** Token verified ***/
				$code_valid = TRUE;
				$error->set('INTRO');
				
				if ( !empty( $_POST ) ) {
					
					$error->reset();
					
					$error->add('P_MISSING',	'You must fill out both password inputs.');
					$error->add('P_MATCH',		'The passwords submitted don\'t match.');
					$error->add('P_LENGTH',		'Your password must be 6-30 characters long.');
					$error->add('P_USERNAME',	'Your password can\'t be the same as your username.');
					
					$f['password']		= isset( $_POST['password'] ) ? $db->escape($_POST['password']) : NULL;
					$f['password_r']	= isset( $_POST['password_r'] ) ? $_POST['password_r'] : NULL;
					
					// Check if password/repeat missing.
					if ( empty($f['password']) || empty($f['password_r']) ) $error->set('P_MISSING');
					else {
						
						// Check if password between 6-30 characters.
						if ( strlen($f['password'])<6 || strlen($f['password'])>30 ) $error->set('P_LENGTH');
						
						// Check if password equals username.
						if ( $f['password'] == $user->info('username', $reset['target_user']) ) $error->append('P_USERNAME');
						
						if ( !$error->exists() ) {
							
							// Check if passwords match.
							if ( $f['password'] != $f['password_r'] ) $error->set('P_MATCH');
							else {
								
								/*** Fields validated ***/
								$code_valid = NULL;
								
								// Set all reset tokens for user to be invalid.
								$db->where(['target_user'=>$reset['target_user'], 'expired'=>0, 'used'=>0])->update('resets', ['expired' => 1]);
								
								// Set current request token to "used" instead of "expired".
								$db->where(['id'=>$reset['id']])->update('resets', ['expired'=>0, 'used'=>1]);
								
								// Hash and store new password.
								$hash = password_hash($f['password'], PASSWORD_DEFAULT);
								$db->where(['id' => $reset['target_user']])->update('users', ['password' => $hash]);
								
								redirect('/login?reset');
								
							} // End: Check if passwords match.
							
						} // End: Continue after checking all fields.
						
					} // End: Check if password/repeat missing.
					
				} // End: Reset form submission.
				
			} // End: Check if reset token has been used.
			
		} // End: Check if reset code has expired.
		
	} // End: Check if reset code exists in database.
	
} // End: // Check if reset code is missing.

?>

<div class="title"><h2>Reset Password</h2></div>
<div class="body">
    <?php echo $error->display(); ?>
<?php if ( isset( $code_valid ) ) { // If the form hasn't been successfully submitted. ?>
    <form action="/reset" method="POST" class="form">
        <div class="group">
            <label for="password">New Password</label>
            <input type="password" name="password" id="password">
        </div>
        <div class="group">
            <label for="password_r">Repeat Password</label>
            <input type="password" name="password_r" id="password_r">
        </div>
        <button type="submit" class="bttn large purple full">Change Password</button>
        <input type="hidden" name="code" value="<?php echo htmlspecialchars($f['code']); ?>" />
    </form>
<?php } // End: If the form hasn't been successfully submitted. ?>
</div>
<div class="footer">
    <a href="/login" class="full">Back to Sign In</a>
</div>

<script>window.onload = function() { document.getElementById('password').focus(); };</script>

<?php show_footer(); ?>