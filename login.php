<?php

/**
  * User Login
**/

require_once('core.php');

// START PREVENT FUTURE LOGINS TEMP CODE
show_header('Sign In', FALSE, ['body_id' => 'login', 'body_class' => 'boxed']);

?>
<div class="header"><h1>Sign In</h1></div>
<div class="body">
    <p style="text-align:center;">Login is currently unavailable due to routine maintenance. Please try again later.</p>
</div>
<div class="footer">
    <div class="bttn-group">
        <a href="/" class="bttn mid" style="width: 100%;">Back to Homepage</a>
    </div>
</div>
<?php
show_footer();
die();
// END PREVENT FUTURE LOGINS TEMP CODE

if ( $user->logged_in() ) redirect('/dashboard');

show_header('Sign In', FALSE, ['body_id' => 'login', 'body_class' => 'boxed']);

$error->add('AUTH',			'Please sign in to continue.', 'warning');
$error->add('LOGOUT',		'You have been securely logged out.', 'success');
$error->add('RESET_DONE',	'Your password has been successfully changed, you may now sign in.', 'success');

// Displaying forced messages based on $_GET requests.
if		( isset( $_GET['auth'] ) )		 $error->force('AUTH');
elseif	( isset( $_GET['logged_out'] ) ) $error->force('LOGOUT');
elseif	( isset( $_GET['reset'] ) )		 $error->force('RESET_DONE');

$f['redirect'] = isset( $_GET['redirect'] ) ? $_GET['redirect'] : NULL;

$error->add('SUSPEND',	'Your account is currently suspended.');

if ( !empty( $_POST ) ) {

	$error->reset();

	$error->add('MISSING',	'Both username &amp; password required.');
	$error->add('INVALID',	'Incorrect username or password.');

	$f['username']	=	isset( $_POST['username'] ) ? $db->escape($_POST['username']) : NULL;
	$f['password']	=	isset( $_POST['password'] ) ? $db->escape($_POST['password']) : NULL;

	$f['redirect']	=	isset( $_POST['redirect'] ) ? $_POST['redirect'] : $f['redirect'];
	$f['remember']	=	isset( $_POST['remember'] ) ? TRUE : FALSE;

	// Check if username or password missing.
	if ( empty($f['username']) || empty($f['password']) ) $error->set('MISSING');
	else {

		// Check if username exists in database.
		if ( !$user->check_username($f['username']) ) $error->set('INVALID');
		else {

			// Verify password provided with database hash.
			if ( !password_verify($f['password'], $user->info('password', $f['username']) ) ) $error->set('INVALID');
			else {

				// Check if user suspended.
				if ( $user->suspended($f['username']) ) $error->set('SUSPEND');
				else {

					// Authenticate the browser.
					$user->auth_set($f['username'], $f['remember']);

					// Update values in database.
					$db->where(['username' => $f['username']])->update('users', ['last_ip' => current_ip(), 'last_login' => time_now()]);

					// Redirect user as needed.
					if ( !empty($f['redirect']) ) redirect($f['redirect']);
					else redirect('/dashboard?welcome');

				} // End: Check if user suspended.

			} // End: Verify password with database hash.

		} // End: Check if username exists in database.

	} // End: Check if username or password missing.

} // End: POST submission.

if ( isset($_GET['suspended']) ) $error->force('SUSPEND');

?>

<div class="header"><h1>Sign In</h1></div>
<div class="body">
    <form action="/login" method="POST">
        <?php echo $error->display(); ?>
        <div class="group">
            <div class="label"><label for="username">Username</label></div>
            <input type="text" name="username" id="username" value="<?php $form->post_val('username'); ?>" spellcheck="false">
        </div>
        <div class="group">
            <div class="label"><label for="password">Password</label></div>
            <input type="password" name="password" id="password">
        </div>
        <div class="submit">
            <button type="submit" class="bttn big green">Sign In</button>
            <div class="remember">
                <input type="checkbox" name="remember" id="remember"<?php if ( isset($f['remember']) && $f['remember'] ) echo ' checked'; ?>>
                <label for="remember">Remember Me</label>
            </div>
        </div>
        <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($f['redirect']); ?>">
    </form>
</div>
<div class="footer">
    <div class="bttn-group">
        <a href="/register" class="bttn mid">Create Account</a>
        <a href="/forgot" class="bttn mid">Forgot Password</a>
    </div>
</div>

<script>window.onload = function() { document.getElementById('<?php echo ( empty( $f['username'] ) ) ? 'username' : 'password'; ?>').focus(); };</script>

<?php show_footer(); ?>
