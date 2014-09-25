<?php

/**
  
  * User Registration
  *
  * Allows users to register a new account, sends an
  * activation email and allows them to login afterwards.
  
**/

require_once('core.php');

// If the user is already logged in, redirect them out of here.
if ( $user->logged_in() ) redirect('/');

show_header('Register', FALSE, ['body_id' => 'boxed', 'modal_class' => 'wide']);

if ( !empty( $_POST ) ) {
	
	$error->add('MISSING',		'You must fill in all the inputs in the form.');
	
	$error->add('U_LENGTH',		'Your username must be 5-20 characters long.');
	$error->add('U_INVALID',	'Your username must start with a letter and contain "A-Z, 0-9, _" characters only.');
	$error->add('U_USED',		'That username is already used by another member.');
	
	$error->add('E_LENGTH',		'Your email cannot be longer than 50 characters.');
	$error->add('E_INVALID',	'The email submitted isn\'t in valid email format.');
	$error->add('E_USED',		'That email is already used by another member.');
	
	$error->add('P_LENGTH',		'Your password must be 6-30 characters long.');
	$error->add('P_USERNAME',	'Your password can\'t be the same as your username.');
	$error->add('P_MATCH',		'The passwords submitted don\'t match.');
	
	$f['username']		= isset( $_POST['username'] ) ? $db->escape($_POST['username']) : NULL;
	$f['email']			= isset( $_POST['email'] ) ? $db->escape($_POST['email']) : NULL;
	$f['password']		= isset( $_POST['password'] ) ? $db->escape($_POST['password']) : NULL;
	$f['password_r']	= isset( $_POST['password_r'] ) ? $_POST['password_r'] : NULL;
	
	// Check if any inputs are missing.
	if ( empty($f['username']) || empty($f['email']) || empty($f['password']) || empty($f['password_r']) ) $error->set('MISSING');
	
	// Username field checks.
	if ( !empty($f['username']) ) {
		
		// Check if username between 5-20 characters.
		if ( strlen($f['username'])<5 || strlen($f['username'])>20 ) $error->append('U_LENGTH');
		
		// Check if username is valid (alphanumeric).
		elseif ( !preg_match('/^[A-Za-z][A-Za-z0-9]*(?:_[A-Za-z0-9]+)*$/', $f['username']) ) $error->append('U_INVALID');
		
		// Check if username is already in use.
		elseif ( $user->check_username($f['username']) ) $error->append('U_USED');
		
	} // End: Username field checks.
	
	// Email field checks.
	if ( !empty($f['email']) ) {
		
		// Check if email under 50 characters.
		if ( strlen($f['email']>=50) ) $error->append('E_LENGTH');
		
		// Check if email is valid.
		elseif ( !is_email($f['email']) ) $error->append('E_INVALID');
		
		// Check if email is in use by another user.
		elseif ( $user->check_email($f['email']) ) $error->append('E_USED');
		
	} // End: Email field checks.
	
	// Password field checks.
	if ( !empty($f['password']) ) {
		
		// Check if password between 6-30 characters.
		if ( strlen($f['password'])<6 || strlen($f['password'])>30 ) $error->append('P_LENGTH');
		
		// Check if password equals username.
		elseif ( $f['password'] == $f['username'] ) $error->append('P_USERNAME');
		
		// Check if passwords match.
		elseif ( $f['password'] != $f['password_r'] ) $error->append('P_MATCH');
		
	} // End: Password field checks.
	
	if ( !$error->exists() ) {
		
		// Choose a random avatar for the user.
		$avatar = ['cow', 'creeper', 'pig', 'skeleton', 'zombie', 'zombiepig'];
		$avatar = 'default_' . $avatar[rand(0,5)] . '.png';
		
		// Generate activation token.
		$code = random_str(15);
		
		// Throw all user info into one array for database push.
		$u = [
			'username'			=> $f['username'],
			'email'				=> $f['email'],
			'password'			=> password_hash($f['password'], PASSWORD_DEFAULT),
			'joined'			=> date('Y-m-d H:i:s'),
			'last_ip'			=> $_SERVER['REMOTE_ADDR'],
			'avatar'			=> $avatar,
			'activate_token'	=> $code
		];
		
		$db->insert('users', $u);
		
		// Email HTML body.
		$email =	"<p>Welcome to MCPE Hub! We can't wait to see what you have to contribute to our growing community, but first we need to activate your account.</p>\n";
		$email .=	"<p>Activate your account by clicking on <a href=\"".MAINURL."activate?code={$code}\">this link</a>.</p>\n";
		$email .=	"<p>Once you've activated your account, you'll be able to publish content, comment and participate in our growing community.</p>\n";
		$email .=	"<p>Thanks for joining our community!</p><p>- MCPE Hub Team</p>\n";
		$email .=	"<p><i>Follow us on Twitter: <a href=\"http://twitter.com/MCPEHubNetwork\">@MCPEHubNetwork</a> for news, updates, giveaways and more!</i></p>\n";
		$email .=	"<div class=\"bottom\">If you have issues opening the link above, use the following link: <a href=\"".MAINURL."activate?code={$code}\">".MAINURL."activate?code={$code}</a></div>\n";
		
		// Format and send email to user.
		$email = $mail->format($email);
		$email = $mail->send($f['email'], $f['username'], 'Activate Your Account', $email);
		
		redirect('/login?registered');
		
	} // End: Continue after checking all fields.
	
} // End: Registration form submission.

?>

<div class="title"><h2>Create an Account</h2></div>
<div class="body">
    <div class="reg-features clearfix">
        <div><i class="fa fa-pencil"></i> Submit Content</div>
        <div><i class="fa fa-comments"></i> Join Discussions</div>
        <div><i class="fa fa-star"></i> Favorite Content</div>
        <div><i class="fa fa-magic"></i> + Much More!</div>
    </div>
    <?php echo $error->display(); ?>
    <form action="/register" method="POST" class="form">
        <div class="half">
            <div class="group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" value="<?php $form->POST_value('username'); ?>" spellcheck="false">
            </div>
            <div class="group">
                <label for="email">Email</label>
                <input type="text" name="email" id="email" value="<?php $form->POST_value('email'); ?>" spellcheck="false">
            </div>
        </div>
        <div class="half last">
            <div class="group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="text">
            </div>
            <div class="group">
                <label for="password_r">Repeat Password</label>
                <input type="password" name="password_r" id="password_r" class="text">
            </div>
        </div>
        <center><button type="submit" class="bttn large purple register">Create Account</button></center>
    </form>
</div>
<div class="footer">
    <a href="/login" class="full">&laquo; Back to Sign In</a>
</div>

<?php if ( empty( $f['username'] ) ) { ?><script>window.onload = function() { document.getElementById('username').focus(); };</script><?php } ?>

<?php show_footer(); ?>