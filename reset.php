<?php

/**
 * Password Reset
 *
 * This page processes any password change requests, after the
 * user clicks the reset link in their email. It will check if
 * the token is valid, and allow them to change their password.
**/

require_once('loader.php');

// If user is already logged in, redirect to dashboard
if ( logged_in() ) redirect('/dashboard');

$page->body_id = 'reset';
$page->body_class = 'boxed';

$page->header('Reset Password');

// Get token from $_GET.
$token = $db->escape(filter_input(INPUT_GET, 'token', FILTER_SANITIZE_STRING));

// Token missing, display error.
if ( $token == null )
	$errors->add('MISSING', 'Reset token is missing from URL.')->force();

else {

	$errors->add('INFO',		'Let\'s get you a new password! Choose a new one below.', 'info');
	$errors->add('INVALID',		'This reset token is invalid or expired.');
	$errors->add('USED',		'This reset token has already been used.');
	$errors->add('SUSPENDED',	'This account is currently suspended and cannot be reset.');
	$errors->add('BANNED',		'This account has been banned and cannot be reset.');

	// Fetch the token from the database.
	$token_db = $db->select()->from('resets')->where('token', $token)->fetch_first();

	// Token not found in the database.
	if ( !$db->affected_rows )
		$errors->force('INVALID');

	// Check if token has been used already.
	elseif ( $token_db['status'] == 1 )
		$errors->force('USED');

	// Check if token status not 0 (unused).
	elseif ( $token_db['status'] != 0 )
		$errors->force('INVALID');

	// Check if token has expired beyond 24 hours.
	elseif ( (strtotime($token_db['created']) + 60*60*24) < time() )
		$errors->force('INVALID');

	// Fetch user info from the database, to ensure this user still exists.
	elseif ( !$user = User::get_by('id', $token_db['user_id']) )
		$errors->force('INVALID');

	// Check if user suspended.
	elseif ( $user['status'] == '-1' )
		$errors->force('SUSPENDED');

	// Check if user banned.
	elseif ( $user['status'] == '-2' )
		$errors->force('BANNED');

	// All checks passed, move onto allowing a reset.
	else {

		$token_valid = true;
		$errors->set('INFO');

		// Token valid, and form submitted for password change.
		if ( submit_POST() ) {

			$password			= input_POST('password');
			$password_repeat	= input_POST('password-repeat');

			$errors->reset();

			$errors->add('MISSING',		'Both password fields must be filled in.');
			$errors->add('MATCH',		'Your passwords didn\'t match.');
			$errors->add('LENGTH',		'Your password must be 6-30 characters long.');
			$errors->add('USERNAME',	'Your password can\'t contain your username.');

			// Check if either field is missing.
			if ( empty($password) || empty($password_repeat) )
				$errors->force('MISSING');

			else {

				// Check if passwords match.
				if ( $password != $password_repeat )
					$errors->append('MATCH');

				// Check if password between 6-30 characters.
				if ( !length($password, 30, 6) )
					$errors->append('LENGTH');

				// Check if password contains username.
				if ( strpos($password, $user['username']) !== false )
					$errors->append('USERNAME');

				// Fields are valid, reset the password.
				if ( !$errors->exist() ) {

					// Invalidate any active tokens associated to this user.
					$db->where(['token' => $token])->update('resets', ['status' => '1']);
					$db->where(['user_id' => $user['id'], 'status' => 0])->update('resets', ['status' => '-1']);

					// Hash and store new password.
					$password = password_hash($password, PASSWORD_DEFAULT);
					$db->where('id', $user['id'])->update('users', ['password' => $password]);

					redirect('/login?m=reset');

				} // END: Fields are valid, reset the password.

			} // END: Check if either field is missing.

		} // END: Token valid, and form submitted for password change.

	} // END: All checks passed, move onto allowing a reset.

}

?>
<div id="logo"><a href="/">MCPE Hub</a></div>
<div id="body">
	<div id="content">
		<h1>Reset password</h1>
		<?php $errors->display(); ?>
<?php if ( isset($token_valid) ) { ?>
		<form action="/reset?token=<?php echo htmlspecialchars($token); ?>" method="POST">
			<input type="password" name="password" id="password" maxlength="30" placeholder="Password">
			<input type="password" name="password-repeat" id="password-repeat" maxlength="30" placeholder="Repeat password">
			<button type="submit">Change password</button>
		</form>
		<script>window.onload = function() { document.getElementById('password').focus(); };</script>
<?php } ?>
	</div>
	<div id="footer"><a href="/login">Back to login</a></div>
</div>
<?php $page->footer(); ?>