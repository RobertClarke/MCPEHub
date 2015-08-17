<?php

/**
 * Account Activation
 *
 * This page allows users to activate their accounts from the
 * activation link provided in their welcome emails. Activation
 * unlocks all the website features for the user.
**/

require_once('loader.php');

$page->body_id = 'activate';
$page->body_class = 'boxed';

$page->header('Activate Account');

// Display messages based on $_GET result.
switch( input_GET('m') ) {
	case 'success':
		$errors->add('SUCCESS', 'Account activated, welcome to MCPE Hub!', 'success')->set();
	break;
}

// Get token from $_GET.
$token = $db->escape(filter_input(INPUT_GET, 'token', FILTER_SANITIZE_STRING));

// Token missing, display error.
if ( $token == null && input_GET('m') != 'success' )
	$errors->add('MISSING', 'Activation token is missing from URL.')->force();

elseif ( input_GET('m') != 'success' ) {

	$errors->reset();

	$errors->add('INVALID',		'This activation token is invalid or expired.');
	$errors->add('ACTIVE',		'Your account has already been activated.', 'success');
	$errors->add('SUSPENDED',	'This account is currently suspended and cannot be activated.');
	$errors->add('BANNED',		'This account has been banned and cannot be activated.');

	// Fetch the token from the database.
	$token_db = $db->select()->from('activations')->where('token', $token)->fetch_first();

	// Token not found in the database.
	if ( !$db->affected_rows )
		$errors->force('INVALID');

	// Check if token has been used already.
	elseif ( $token_db['status'] == 1 )
		$errors->force('ACTIVE');

	// Check if token status not 0 (unused).
	elseif ( $token_db['status'] != 0 )
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

	// All checks passed, move onto allowing an activation.
	else {

		$token_valid = true;

		// Update token to used, activate the user.
		$db->where(['token' => $token])->update('activations', ['status' => 1, 'used' => $now]);
		$db->where(['id' => $user['id']])->update('users', ['status' => 1]);

		redirect('/activate?m=success');

	}

}

?>
<div id="logo"><a href="/">MCPE Hub</a></div>
<div id="body">
	<div id="content">
		<h1>Account activation</h1>
		<?php $errors->display(); ?>
	</div>
	<div id="footer"><a href="/login">Back to login</a></div>
</div>