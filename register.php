<?php

/**
 * User Registration
 *
 * This page handles new user registrations for the website.
 * Sends an activation email to the user upon a successful
 * form submission.
**/

require_once('loader.php');

// If user is already logged in, redirect to dashboard
if ( logged_in() ) redirect('/dashboard');

$page->body_id = 'register';
$page->body_class = 'boxed';

$page->header('Register');

$username			= input_POST('username');
$email				= input_POST('email');
$password			= input_POST('password');
$password_repeat	= input_POST('password-repeat');

if ( submit_POST() ) {

	$errors->add('MISSING',		'You must fill in all inputs in the form.');

	$errors->add('U_INVALID',	'Username must start with a letter and contain "a-z, 0-9, _" characters only.');
	$errors->add('U_LENGTH',	'Username must be 5-20 characters long.');
	$errors->add('U_USED',		'Username is already in use by another member.');

	$errors->add('E_INVALID',	'Email you entered isn\'t a valid email address.');
	$errors->add('E_LENGTH',	'Email cannot be more than 50 characters long.');
	$errors->add('E_USED',		'Email is already in use by another member.');

	$errors->add('P_USERNAME',	'Password can\'t contain your username.');
	$errors->add('P_LENGTH',	'Password must be 6-30 characters long.');
	$errors->add('P_MATCH',		'Your passwords didn\'t match.');

	// Check if any form fields missing.
	if ( empty($username) || empty($email) || empty($password) || empty($password_repeat) )
		$errors->force('MISSING');

	else {

		// Username field checks.
		if 		( !alphanum($username) )					$errors->append('U_INVALID');
		elseif	( !length($username, 20, 5) )				$errors->append('U_LENGTH');
		elseif	( !username_avail($username) )				$errors->append('U_USED');

		// Email field checks.
		if 		( !is_email($email) )						$errors->append('E_INVALID');
		elseif	( !length($email, 50) )						$errors->append('E_LENGTH');
		elseif	( !email_avail($email) )					$errors->append('E_USED');

		// Password field checks.
		if 		( !length($password, 30, 6) )				$errors->append('P_LENGTH');
		elseif	( strpos($password, $username) !== false )	$errors->append('P_USERNAME');
		elseif	( $password != $password_repeat )			$errors->append('P_MATCH');

		if ( !$errors->exist() ) {

			// Generate activation token.
			$token = random_string(15);

			// Pick an avatar for the new user.
			$avatar = ['cow', 'creeper', 'enderman', 'pig', 'skeleton', 'slime', 'zombie', 'zombiepig'];
			$avatar = 'default_' . $avatar[rand(0,7)] . '.png';

			// Insert user into the database.
			$insert_user = [
				'username'		=> $username,
				'email'			=> $email,
				'password'		=> password_hash($password, PASSWORD_DEFAULT),
				'status'		=> 0,
				'permission'	=> 0,
				'avatar'		=> $avatar,
				'joined'		=> $now,
				'last_ip'		=> filter_input(INPUT_SERVER, 'REMOTE_ADDR')
			];
			$user_id = $db->insert('users', $insert_user);

			// Create activation token for user.
			$insert_activation = [
				'user_id'		=> $user_id,
				'token'			=> $token,
				'status'		=> 0
			];
			$db->insert('activations', $insert_activation);

			// Create profile for user.
			$db->insert('users_profiles', ['user_id' => $user_id]);

			// Create Smarty instance for email.
			$smarty = new Smarty;

			$smarty->assign('username', $username);
			$smarty->assign('link', SITEURL.'/activate?token='.$token);

			// Generate and send email.
			$sender = new Email( $email, 'Activate Your Account', $username );
			$sender->add_smarty($smarty)->set_template('activate')->send();

			redirect('/welcome');

		} // END: Check if any form fields missing.

	} // END: Check if any form fields missing.

}

?>
<div id="logo"><a href="/">MCPE Hub</a></div>
<div id="body">
	<div id="content">
		<h1>Create an account</h1>
		<?php $errors->display(); ?>
		<form action="/register" method="POST">
			<input type="text" name="username" id="username" maxlength="20" placeholder="Username" value="<?php echo htmlspecialchars($username); ?>" spellcheck="false">
			<input type="email" name="email" id="email" maxlength="50" placeholder="Email address" value="<?php echo htmlspecialchars($email); ?>" spellcheck="false">
			<input type="password" name="password" id="password" maxlength="30" placeholder="Password">
			<input type="password" name="password-repeat" id="password-repeat" maxlength="30" placeholder="Repeat password">
			<button type="submit">Create Account</button>
			<p class="accept">You accept our <a href="/tos" target="_blank">terms</a> and <a href="/privacy" target="_blank">privacy policy</a></p>
		</form>
	</div>
	<div id="footer"><a href="/login">I already have an account</a></div>
</div>
<?php if ( empty($username) ) { ?><script>window.onload = function() { document.getElementById('username').focus(); };</script><?php } ?>
<?php $page->footer(); ?>