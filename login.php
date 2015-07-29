<?php

/**
 * User Login
 *
 * This page handles the front end of user authentication on the
 * website. It relies on backend functions from the User class in
 * order to authenticate the user.
**/

require_once('loader.php');

// If user is already logged in, redirect to dashboard
if ( logged_in() ) redirect('/dashboard');

$page->body_id = 'login';
$page->body_class = 'boxed';

$page->header('Sign In');

// Display messages based on $_GET result.
switch( input_GET('m') ) {
	case 'logout':
		$errors->add('AUTH_LOGOUT', 'You have been securely logged out.', 'success')->set();
	break;
	case 'auth':
		$errors->add('AUTH_REQ', 'Please login to continue.', 'warning')->set();
	break;
	case 'reset':
		$errors->add('AUTH_RESET', 'Your password has been changed! You can now login with it.', 'success')->set();
	break;
}

$username = input_POST('username');
$remember = filter_input(INPUT_POST, 'remember', FILTER_VALIDATE_BOOLEAN);
$redirect = filter_input(INPUT_GET, 'redirect', FILTER_SANITIZE_STRING);

if ( submit_POST() ) {

	// Switch redirect to POST value
	$redirect = filter_input(INPUT_POST, 'redirect', FILTER_SANITIZE_STRING);

	$result = login();

	if ( !is_error($result) ) {

		if ( !empty($redirect) )
			redirect($redirect);
		else
			redirect('/dashboard?m=welcome');

	}

	// Force the error message to the form error object.
	else
		$errors->add_object($result)->force();

}

?>
<div id="logo"><a href="/">MCPE Hub</a></div>
<div id="body">
	<div id="content">
		<h1>Welcome back!</h1>
		<?php $errors->display(); ?>
		<form action="/login" method="POST">
			<input type="text" name="username" id="username" maxlength="20" placeholder="Username" value="<?php echo htmlspecialchars($username); ?>" spellcheck="false">
			<input type="password" name="password" id="password" maxlength="30" placeholder="Password">
			<button type="submit">Login</button>
			<p class="accept">
				<span class="left">
					<input type="checkbox" name="remember" id="remember" value="true"<?php if ( $remember ) echo ' checked' ?>>
					<label for="remember">Remember me</label>
				</span>
				<span class="right"><a href="/forgot">Forgot password?</a></span>
			</p>
			<input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect); ?>">
		</form>
	</div>
	<div id="footer"><a href="/register">Don't have an account?</a></div>
</div>
<script>window.onload = function() { document.getElementById('<?php echo empty($username) ? 'username' : 'password'; ?>').focus(); };</script>
<?php $page->footer(); ?>