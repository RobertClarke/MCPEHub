<?php

/**
  * User Registration
**/

require_once('core.php');
if ( $user->logged_in() ) redirect('/dashboard');

show_header('Register', FALSE, ['body_id' => 'register', 'body_class' => 'boxed wide']);

if ( !empty($_POST) ) {
	
	$error->add('MISSING',		'You must fill in all the inputs in the form.');
	
	$error->add('U_LENGTH',		'Your username must be 5-20 characters long.');
	$error->add('U_INVALID',	'Your username must start with a letter and contain "A-Z, 0-9, _" characters only.');
	$error->add('U_USED',		'Your username is already in use by another member.');
	
	$error->add('E_LENGTH',		'Your email cannot be longer than 50 characters.');
	$error->add('E_INVALID',	'Your email isn\'t in valid email format.');
	$error->add('E_USED',		'Your email is already in use by another member.');
	
	$error->add('P_LENGTH',		'Your password must be 6-30 characters long.');
	$error->add('P_USERNAME',	'Your password can\'t be the same as your username.');
	$error->add('P_MATCH',		'Your passwords didn\'t match.');
	
	$f['username']		= isset( $_POST['username'] )	? $db->escape($_POST['username']) : NULL;
	$f['email']			= isset( $_POST['email'] )		? $db->escape($_POST['email']) : NULL;
	$f['password']		= isset( $_POST['password'] )	? $db->escape($_POST['password']) : NULL;
	$f['password2']		= isset( $_POST['password2'] )	? $_POST['password2'] : NULL;
	
	// Check if any inputs missing.
	if ( empty($f['username']) || empty($f['email']) || empty($f['password']) || empty($f['password2']) ) $error->set('MISSING');
	else {
		
		// USERNAME field checks.
		if ( !empty($f['username']) ) {
			if		( !$form->length($f['username'], 20, 5) )	$error->append('U_LENGTH');
			elseif	( !$form->alphanum($f['username']) )		$error->append('U_INVALID');
			elseif	( $user->check_username($f['username']) )	$error->append('U_USED');
		}
		
		// EMAIL field checks.
		if ( !empty($f['email']) ) {
			if		( !$form->length($f['email'], 50) )			$error->append('E_LENGTH');
			elseif	( !is_email($f['email']) )					$error->append('E_INVALID');
			elseif	( $user->check_email($f['email']) )			$error->append('E_USED');
		}
		
		// PASSWORD field checks.
		if ( !empty($f['password']) ) {
			if		( !$form->length($f['password'], 30, 6) )	$error->append('P_LENGTH');
			elseif	( $f['password'] == $f['username'] )		$error->append('P_USERNAME');
			elseif	( $f['password'] != $f['password2'] )		$error->append('P_MATCH');
		}
		
		if ( !$error->exists() ) {
			
			// Select a random avatar for user.
			$avatar = ['cow', 'creeper', 'pig', 'skeleton', 'zombie', 'zombiepig'];
			$avatar = 'default_'.$avatar[rand(0,5)].'.png';
			
			// Generate activation code.
			$token = random_str(15);
			
			$u = [
				'username'			=> $f['username'],
				'password'			=> password_hash($f['password'], PASSWORD_DEFAULT),
				'email'				=> $f['email'],
				'joined'			=> time_now(),
				'last_ip'			=> current_ip(),
				'avatar'			=> $avatar,
				'activate_token'	=> $token
			];
			
			// Insert user to database.
			$db->insert('users', $u);
			
			// Send welcome email to user with activation instructions.
			$e_body	 = "<p>Welcome to MCPE Hub! We can't wait to see what you have to contribute to our growing community, but first you need to activate your account.</p>\n";
			$e_body .= "<p>Activate your account by clicking on <a href=\"".MAINURL."activate?code={$token}\">this link</a>.</p>\n";
			$e_body .= "<p>Once you've activated your account, you'll be able to publish content, comment and participate in our growing community.</p>\n";
			$e_body .= "<p>Thanks for joining our community!</p><p>- MCPE Hub Team</p>\n";
			$e_body .= "<p><i>Follow us on Twitter: <a href=\"http://twitter.com/MCHubCommunity\">@MCHubCommunity</a> for news, updates, giveaways and more!</i></p>\n";
			$e_body .= "<div class=\"bottom\">If you have issues opening the link above, use the following link: <a href=\"".MAINURL."activate?code={$token}\">".MAINURL."activate?code={$token}</a></div>\n";
			
			$email = $mail->format($e_body);
			$email = $mail->send($f['email'], $f['username'], 'Activate Your Account', $email);
			
			redirect('/welcome');
			
		} // End: No errors found in inputs.
		
	} // End: Check if any inputs missing
	
} // End: POST submission.

?>

<div class="header"><h1>Create an Account</h1></div>
<div class="body">
    <div class="features">
        <span><i class="fa fa-upload"></i> Upload Content</span>
        <span><i class="fa fa-comments"></i> Join Discussions</span>
        <span><i class="fa fa-thumbs-up"></i> Like Posts</span>
        <span><i class="fa fa-magic"></i> + Much More!</span>
    </div>
    <form action="/register" method="POST">
        <?php echo $error->display(); ?>
        <div class="half">
            <div class="group">
                <div class="label"><label for="username">Username</label></div>
                <input type="text" name="username" id="username" value="<?php $form->post_val('username'); ?>">
            </div>
            <div class="group">
                <div class="label"><label for="email">Email Address</label></div>
                <input type="text" name="email" id="email" value="<?php $form->post_val('email'); ?>">
            </div>
        </div>
        <div class="half last">
            <div class="group">
                <div class="label"><label for="password">Password</label></div>
                <input type="password" name="password" id="password">
            </div>
            <div class="group">
                <div class="label"><label for="password2">Repeat Password</label></div>
                <input type="password" name="password2" id="password2">
            </div>
        </div>
        <div class="submit">
            <button type="submit" class="bttn big green"><i class="fa fa-check"></i> Create Account</button>
        </div>
    </form>
</div>
<div class="footer">
    <a href="/login" class="bttn mini">Already Have An Account?</a>
</div>

<?php if ( empty( $f['username'] ) ) { ?><script>window.onload = function() { document.getElementById('username').focus(); };</script><?php } ?>

<?php show_footer(); ?>
