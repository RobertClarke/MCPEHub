<?php

/**
 * Forgot Password Form
 *
 * This page is used for users to request their passwords for
 * reset. If the user enters a valid e-mail address, the system
 * will e-mail them a link to reset their account password.
**/

require_once('loader.php');

// If user is already logged in, redirect to dashboard
if ( logged_in() ) redirect('/dashboard');

$page->body_id = 'forgot';
$page->body_class = 'boxed';

$page->header('Forgot Password');

// Set success message, if required.
if ( input_GET('success') !== null )
	$errors->add('SUCCESS', 'We sent you an email with instructions on how to reset your password.', 'success')->force();

$email = input_POST('email');

// Check number of times user has requested for the day.
$ip = filter_input(INPUT_SERVER, 'REMOTE_ADDR');
$db->select('id')->from('resets')->limit(5)->where('`ip`="'.$ip.'" AND DATE(`created`)="'.$today.'"')->fetch();

// User has done over 5 requests today, display an error.
if ( $db->affected_rows > 4 ) {
	$limit_reached = true;
	$errors->add('LIMIT', 'You\'ve made too many reset requests for today.')->set();
}

elseif ( submit_POST() ) {

	$errors->add('MISSING',		'You must enter your email.');
	$errors->add('INVALID',		'Email entered isn\'t a valid email address.');
	$errors->add('NOEMAIL',		'That email isn\'t associated with an account.');
	$errors->add('SUSPENDED',	'This account is currently suspended and cannot be reset.');
	$errors->add('BANNED',		'This account has been banned and cannot be reset.');

	// Check if email missing.
	if ( empty($email) )
		$errors->force('MISSING');

	// Check if email is valid.
	elseif ( !is_email($email) )
		$errors->force('INVALID');

	// Check if email exists.
	elseif ( !$user = User::get_by('email', $email) )
		$errors->force('NOEMAIL');

	// Check if user suspended.
	elseif ( $user['status'] == '-1' )
		$errors->force('SUSPENDED');

	// Check if user banned.
	elseif ( $user['status'] == '-2' )
		$errors->force('BANNED');

	// All basic checks passed, check for # of requests from IP (anti-spam).
	else {

		// Generate and store token.
		$token = random_string(15);

		$insert_token = [
			'user_id'	=> $user['id'],
			'token'		=> $token,
			'status'	=> 0,
			'created'	=> $now,
			'ip'		=> $ip
		];

		$db->insert('resets', $insert_token);

		// Create Smarty instance for email.
		$smarty = new Smarty;

		$smarty->assign('username', $user['username']);
		$smarty->assign('link', SITEURL.'/reset?token='.$token);

		// Generate and send email.
		$sender = new Email( $email, 'Reset Your Password', $user['username'] );
		$sender->add_smarty($smarty)->set_template('reset')->send();

		redirect('/forgot?success');

	}
}

?>
<div id="logo"><a href="/">MCPE Hub</a></div>
<div id="body">
	<div id="content">
		<h1>Forgot password</h1>
		<?php $errors->display(); ?>
<?php if ( input_GET('success') === null && !isset($limit_reached) ) { ?>
		<form action="/forgot" method="POST">
			<input type="text" name="email" id="email" maxlength="50" placeholder="Your email address" value="<?php echo htmlspecialchars($email); ?>" spellcheck="false">
			<button type="submit">Reset password</button>
		</form>
		<script>window.onload = function() { document.getElementById('email').focus(); };</script>
<?php } ?>
	</div>
	<div id="footer"><a href="/login">Back to login</a></div>
</div>
<?php $page->footer(); ?>