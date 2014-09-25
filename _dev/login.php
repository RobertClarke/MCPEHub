<?php

/**
  
  * User Authentication
  *
  * Used to log users in & out around the website.
  
**/

require_once('core.php');

// If the user is already logged in, redirect them out of here.
if ( $user->logged_in() ) {
	if ( isset( $_GET['logout'] ) ) $user->logout(); // Log user out.
	else redirect('/');
}

show_header('Sign In', FALSE, ['body_id' => 'boxed']);

// Showing error messages based on actions.
$error->add('LOGOUT',		'You have been securely logged out.', 'success');
$error->add('REGISTERED',	'<b>Welcome to MCPE Hub!</b><br>You may now sign in with your new username and password.', 'success');
$error->add('AUTH_REQ',		'You must sign in to continue.', 'warning');
$error->add('RESET_DONE',	'Your password has been changed, you may now sign in.', 'success');

if		( isset( $_GET['logged_out'] ) ) $error->force('LOGOUT');
elseif	( isset( $_GET['registered'] ) ) $error->force('REGISTERED');
elseif	( isset( $_GET['auth_req'] ) )   $error->force('AUTH_REQ');
elseif	( isset( $_GET['reset'] ) )		 $error->force('RESET_DONE');

$f['redirect'] = isset( $_GET['redirect'] ) ? $_GET['redirect'] : '';

if ( !empty( $_POST ) ) {
	
	$error->reset();
	
	$error->add('MISSING',	'Both username &amp; password required.');
	$error->add('INVALID',	'Invalid username or password.');
	$error->add('SUSPEND',	'Your account is currently suspended.');
	
	$f['username']	=	isset( $_POST['username'] ) ? $db->escape($_POST['username']) : '';
	$f['password']	=	isset( $_POST['password'] ) ? $db->escape($_POST['password']) : '';
	
	$f['redirect']	=	isset( $_POST['redirect'] ) ? $_POST['redirect'] : $f['redirect'];
	$f['remember']	=	isset( $_POST['remember'] ) ? TRUE : FALSE;
	
	// Check if username/password missing.
	if ( empty($f['username']) || empty($f['password']) ) $error->set('MISSING');
	else {
		
		// Check if username exists in database.
		if ( !$user->check_username($f['username']) ) $error->set('INVALID');
		else {
			
			// Verify password with database hash.
			if ( !password_verify( $f['password'], $user->info('password', $f['username']) ) ) $error->set('INVALID');
			else {
				
				// Check if user suspended.
				if ( $user->suspended($f['username']) ) $error->set('SUSPEND');
				else {
					
					/*** Login success! :) ***/
					
					$user->auth_set($f['username'], $f['remember']);
					
					$update = array(
						'last_ip'		=> $_SERVER['REMOTE_ADDR'],
						'last_login'	=> date('Y-m-d H:i:s')
					);
					$db->where( ['username' => $f['username']] )->update('users', $update);
					
					// Redirect, as needed.
					if ( !empty( $f['redirect'] ) ) redirect($f['redirect']);
					else redirect('/dashboard?login');
					
				} // End: Check if user suspended.
				
			} // End: Verify password with database hash.
			
		} // End: Check if username exists in database.
		
	} // End: Check if username/password missing.
	
} // End: Login form submission.

?>

<div class="title"><h2>Sign In</h2></div>
<div class="body">
    <?php echo $error->display(); ?>
    <form action="/login" method="POST" class="form">
        <div class="group">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" value="<?php $form->POST_value('username'); ?>" spellcheck="false">
        </div>
        <div class="group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" class="text">
        </div>
        <button type="submit" class="bttn large purple login">Sign In</button>
        <div class="check">
            <input type="checkbox" name="remember" id="remember"<?php if ( isset($f['remember']) && $f['remember'] ) echo ' checked'; ?>>
            <label for="remember">Remember Me</label>
        </div>
        <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($f['redirect']); ?>" />
    </form>
</div>
<div class="footer">
    <a href="/register" class="register">Create an Account</a>
    <a href="/forgot">Forgot Password</a>
</div>

<script>window.onload = function() { document.getElementById('<?php echo ( empty( $f['username'] ) ) ? 'username' : 'password'; ?>').focus(); };</script>

<?php show_footer(); ?>